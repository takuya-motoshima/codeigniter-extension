<?php
namespace X\Library;
use \X\Util\Loader;
use \X\Util\FileHelper;
use \X\Util\ArrayHelper;
use \X\Util\Logger;

class SessionDatabaseDriver extends \CI_Session_database_driver {
  public function __construct(&$params) {
    parent::__construct($params);
    $this->_config['table_additional_columns'] = Loader::config('config', 'sess_table_additional_columns');
  }

  /**
   * Read.
   */
  public function read($session_id) {
    try {
      if ($this->_get_lock($session_id) === FALSE)
        return $this->_failure;
      $this->_db->reset_query();
      $this->_session_id = $session_id;
      $this->_db
        ->select('data')
        ->from($this->_config['save_path'])
        ->where('id', $session_id);
      if ($this->_config['match_ip'])
        $this->_db->where('ip_address', $_SERVER['REMOTE_ADDR']);
      if (!($result = $this->_db->get()) OR ($result = $result->row()) === NULL) {
        $this->_row_exists = FALSE;
        $this->_fingerprint = md5('');
        return '';
      }
      $result = ($this->_platform === 'postgre') ? base64_decode(rtrim($result->data)) : $result->data;
      $this->_fingerprint = md5($result);
      $this->_row_exists = TRUE;
      return $result;
    } catch (\Throwable $e) {
      Logger::error($e->getMessage());
      throw $e;
    }
  }

  /**
   * Write.
   */
  public function write($session_id, $session_data) {
    try {
      $this->_db->reset_query();
      if (isset($this->_session_id) && $session_id !== $this->_session_id) {
        if (!$this->_release_lock() OR !$this->_get_lock($session_id))
          return $this->_failure;
        $this->_row_exists = FALSE;
        $this->_session_id = $session_id;
      } elseif ($this->_lock === FALSE)
        return $this->_failure;
      if ($this->_row_exists === FALSE) {
        $insert_data = [
          'id' => $session_id,
          'ip_address' => $_SERVER['REMOTE_ADDR'],
          'timestamp' => time(),
          'data' => ($this->_platform === 'postgre' ? base64_encode($session_data) : $session_data)
        ];
        if (!empty($this->_config['table_additional_columns']))
          $insert_data = $this->addAdditionalColumnsToTableData($insert_data, $session_data);
        if ($this->_db->insert($this->_config['save_path'], $insert_data)) {
          $this->_fingerprint = md5($session_data);
          $this->_row_exists = TRUE;
          return $this->_success;
        }
        return $this->_failure;
      }
      $this->_db->where('id', $session_id);
      if ($this->_config['match_ip'])
        $this->_db->where('ip_address', $_SERVER['REMOTE_ADDR']);
      $update_data = ['timestamp' => time()];
      if ($this->_fingerprint !== md5($session_data))
        $update_data['data'] = ($this->_platform === 'postgre') ? base64_encode($session_data) : $session_data;
      if (!empty($this->_config['table_additional_columns']))
        $update_data = $this->addAdditionalColumnsToTableData($update_data, $session_data);
      if ($this->_db->update($this->_config['save_path'], $update_data)) {
        $this->_fingerprint = md5($session_data);
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
   */
  private function addAdditionalColumnsToTableData(array $insert_data, string $session_data): array {
    $additionalColumns = !is_array($this->_config['table_additional_columns']) ? [$this->_config['table_additional_columns']] : $this->_config['table_additional_columns'];
    $defaultColumns = $this->_db->list_fields($this->_config['save_path']);
    $unserialized = $this->unserialize($session_data);
    if (empty($unserialized))
      $unserialized = [];
    foreach ($additionalColumns as $additionalColumn) {
      if (in_array($additionalColumn, $defaultColumns)) {
        $additionalColumnValue = ArrayHelper::searchArrayByKey($additionalColumn, $unserialized);
        $insert_data[$additionalColumn] = $additionalColumnValue;
      } else
        throw new \RuntimeException("Column {$additionalColumn} is not found in the session table");
    }
    return $insert_data;
  }
}