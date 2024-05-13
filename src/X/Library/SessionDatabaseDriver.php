<?php
namespace X\Library;
use \X\Util\Loader;
use \X\Util\FileHelper;
use \X\Util\ArrayHelper;
use \X\Util\Logger;

/**
 * CI_Session_database_driver extension.
 * Added reading of additional columns (application/config/config.php - sess_table_additional_columns) to be stored in the session management table.
 */
class SessionDatabaseDriver extends \CI_Session_database_driver {
  /**
   * Initialize SessionDatabaseDriver.
   * @param array $params Configuration parameters.
   */
  public function __construct(&$params) {
    parent::__construct($params);
    $this->_config['table_additional_columns'] = Loader::config('config', 'sess_table_additional_columns');
  }

  /**
   * Reads session data and acquires a lock.
   * @param string $sessionId Session ID.
   * @return string Serialized session data.
   */
  public function read($sessionId) {
    if ($this->_get_lock($sessionId) === false)
      return $this->_failure;

    // Prevent previous QB calls from messing with our queries.
    $this->_db->reset_query();

    // Needed by write() to detect session_regenerate_id() calls.
    $this->_session_id = $sessionId;
    $this->_db
      ->select('data')
      ->from($this->_config['save_path'])
      ->where('id', $sessionId);
    if ($this->_config['match_ip'])
      $this->_db->where('ip_address', $_SERVER['REMOTE_ADDR']);
    if (!($result = $this->_db->get()) OR ($result = $result->row()) === null) {
      // PHP7 will reuse the same SessionHandler object after ID regeneration, so we need to explicitly set this to FALSE instead of relying on the default ...
      $this->_row_exists = false;
      $this->_fingerprint = md5('');
      return '';
    }

    // PostgreSQL's variant of a BLOB datatype is Bytea, which is a PITA to work with, so we use base64-encoded data in a TEXT field instead.
    $result = ($this->_platform === 'postgre')
      ? base64_decode(rtrim($result->data))
      : $result->data;
    $this->_fingerprint = md5($result);
    $this->_row_exists = true;
    return $result;
  }

  /**
   * Writes (create / update) session data
   * @param string $sessionId Session ID.
   * @param string $sessionData Serialized session data.
   * @return bool Whether the session was successfully written or not.
   */
  public function write($sessionId, $sessionData) {
    try {
      // Prevent previous QB calls from messing with our queries.
      $this->_db->reset_query();

      // Was the ID regenerated?
      if (isset($this->_session_id) && $sessionId !== $this->_session_id) {
        if (!$this->_release_lock() OR !$this->_get_lock($sessionId))
          return $this->_failure;
        $this->_row_exists = false;
        $this->_session_id = $sessionId;
      } elseif ($this->_lock === false)
        return $this->_failure;
      if ($this->_row_exists === false) {
        $insertData = [
          'id' => $sessionId,
          'ip_address' => $_SERVER['REMOTE_ADDR'],
          'timestamp' => time(),
          'data' => ($this->_platform === 'postgre' ? base64_encode($sessionData) : $sessionData)
        ];
        if (!empty($this->_config['table_additional_columns']))
          $insertData = $this->addAdditionalColumnsToTableData($insertData, $sessionData);
        if ($this->_db->insert($this->_config['save_path'], $insertData)) {
          $this->_fingerprint = md5($sessionData);
          $this->_row_exists = true;
          return $this->_success;
        }
        return $this->_failure;
      }
      $this->_db->where('id', $sessionId);
      if ($this->_config['match_ip'])
        $this->_db->where('ip_address', $_SERVER['REMOTE_ADDR']);
      $updateData = ['timestamp' => time()];
      if ($this->_fingerprint !== md5($sessionData))
        $updateData['data'] = ($this->_platform === 'postgre')
          ? base64_encode($sessionData)
          : $sessionData;
      if (!empty($this->_config['table_additional_columns']))
        $updateData = $this->addAdditionalColumnsToTableData($updateData, $sessionData);
      if ($this->_db->update($this->_config['save_path'], $updateData)) {
        $this->_fingerprint = md5($sessionData);
        return $this->_success;
      }
      return $this->_failure;
    } catch (\Throwable $e) {
      Logger::error($e->getMessage());
      return $this->_failure;
    }
  }

  /**
   * Unserialize the session.
   * @param string $data Serialized session data.
   * @return array|null Unserialized session data.
   */
  private function unserialize(string $data): ?array {
    $fieldset = preg_split('/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\|/', $data, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    if (empty($fieldset))
      return null;
    for ($i=0,$length=count($fieldset); $i<$length; $i+=2)
      $result[$fieldset[$i]] = unserialize($fieldset[$i+1]);
    return $result;
  }

  /**
   * Add additional columns to table data.
   * @param array $insertData Data to be registered in the session table.
   * @param string $sessionData Session data.
   * @return array Registration data for session tables with additional column information.
   */
  private function addAdditionalColumnsToTableData(array $insertData, string $sessionData): array {
    $additionalColumns = !is_array($this->_config['table_additional_columns'])
      ? [$this->_config['table_additional_columns']]
      : $this->_config['table_additional_columns'];
    $defaultColumns = $this->_db->list_fields($this->_config['save_path']);
    $unserialized = $this->unserialize($sessionData);
    if (empty($unserialized))
      $unserialized = [];
    foreach ($additionalColumns as $additionalColumn) {
      if (in_array($additionalColumn, $defaultColumns)) {
        $additionalColumnValue = ArrayHelper::searchArrayByKey($additionalColumn, $unserialized);
        $insertData[$additionalColumn] = $additionalColumnValue;
      } else
        throw new \RuntimeException("Column {$additionalColumn} is not found in the session table");
    }
    return $insertData;
  }
}