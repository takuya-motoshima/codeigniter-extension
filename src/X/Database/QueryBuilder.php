<?php
namespace X\Database;

/**
 * Query Builder.
 */
#[\AllowDynamicProperties]
abstract class QueryBuilder extends \CI_DB_query_builder {
  /**
   * Initialize query builder.
   * @param mixed $config DB Configuration.
   */
  public function __construct($config) {
    parent::__construct($config);
  }

  /**
   * Insert_On_Duplicate_Key_Update.
   * @param string $table (optional) Table name.
   * @param array|object $set (optional) an associative array of insert values.
   * @param bool $escape (optional) Whether to escape values and identifiers.
   * @return int Insert ID.
   */
  public function insert_on_duplicate_update($table='', $set=null, $escape=null): int {
    if ($set !== null)
      parent::set($set, '', $escape);
    if (count($this->qb_set) === 0)
      // No valid data array. Folds in cases where keys and values did not match up
      return ($this->db_debug) ? parent::display_error('db_must_use_set') : false;
    if ($table === '') {
      if (!isset($this->qb_from[0]))
        return ($this->db_debug) ? parent::display_error('db_must_set_table') : false;
      $table = $this->qb_from[0];
    }
    $sql = $this->_insert_on_duplicate_update(
      parent::protect_identifiers($table, true, $escape, false),
      array_keys($this->qb_set),
      array_values($this->qb_set)
    );
    $this->query($sql);
    parent::_reset_write();
    return (int) $this->insert_id();
  }

  /**
   * Insert_On_Duplicate_Key_Update_Batch.
   * @param string $table (optional) Table name.
   * @param array|object $set (optional) an associative array of insert values.
   * @param bool $escape (optional) Whether to escape values and identifiers.
   * @param int $batchSize (optional) Count of rows to insert at once. Default is 100.
   * @return int Number of rows inserted or false on failure.
   */
  public function insert_on_duplicate_update_batch(string $table='', $set=null, bool $escape=null, int $batchSize=100): int {
    if ($set !== null)
      parent::set_insert_batch($set, '', $escape);
    if (count($this->qb_set) === 0)
      // No valid data array. Folds in cases where keys and values did not match up
      return ($this->db_debug) ? parent::display_error('db_must_use_set') : false;
    if ($table === '') {
      if (!isset($this->qb_from[0]))
        return ($this->db_debug) ? parent::display_error('db_must_set_table') : false;
      $table = $this->qb_from[0];
    }

    // Batch this baby
    $affectedRows = 0;
    for ($i = 0, $total = count($this->qb_set); $i < $total; $i += $batchSize) {
      $sql = $this->_insert_on_duplicate_update_batch(
        parent::protect_identifiers($table, true, $escape, false),
        $this->qb_keys,
        array_slice($this->qb_set, $i, $batchSize)
      );
      $this->query($sql);
      $affectedRows += $this->affected_rows();
    }
    parent::_reset_write();
    return $affectedRows;
  }

  /**
   * Insert.
   * @param string $table (optional) Table name.
   * @param array|object $set (optional) An associative array of field/value pairs.
   * @param bool $escape (optional) Whether to escape values and identifiers.
   * @return int Insert ID.
   */
  public function insert($table='', $set=null, $escape=null): int {
    $result = parent::insert($table, $set, $escape);
    if ($result === false) {
      $error = parent::error();
      throw new \RuntimeException($error['message'], $error['code']);
    }
    return (int) $this->insert_id();
  }

  /**
   * Insert_Batch.
   * @param string $table Table name.
   * @param array|object $set (optional) Data to insert.
   * @param bool $escape (optional) Whether to escape values and identifiers.
   * @param int $batchSize (optional) Count of rows to insert at once. Default is 100.
   * @return int[] Insert ID.
   */
  public function insert_batch($table, $set=null, $escape=null, $batchSize=100):array {
    if (parent::insert_batch($table, $set, $escape, $batchSize) === false) {
      $error = parent::error();
      throw new \RuntimeException($error['message'], $error['code']);
    }
    $firstId = $this->insert_id();
    return range($firstId, $firstId + count($set) - 1);
  }

  /**
   * Update.
   * @param string $table (optional) Table name.
   * @param array|object $set (optional) An associative array of field/value pairs.
   * @param string|array $where (optional) The WHERE clause.
   * @param int $limit (optional) The LIMIT clause.
   * @return void
   */
  public function update($table='', $set=null, $where=null, $limit=null): void {
    if (parent::update($table, $set, $where, $limit) === false) {
      $error = parent::error();
      throw new \RuntimeException($error['message'], $error['code']);
    }
  }

  /**
   * Update_Batch.
   * @param string $table Table name.
   * @param array|object $set (optional) Field name, or an associative array of field/value pairs.
   * @param string $value (optional)  Field value, if $set is a single field.
   * @param int $batchSize (optional) Count of rows to update at once. Default is 100.
   * @return int Number of rows updated or FALSE on failure
   */
  public function update_batch($table, $set=null, $value=null, $batchSize=100):int {
    $affectedRows = parent::update_batch($table, $set, $value, $batchSize);
    if ($affectedRows === false) {
      $error = parent::error();
      throw new \RuntimeException($error['message'], $error['code']);
    }
    return $affectedRows;
  }

  /**
   * Execute the query.
   * @param string $sql The SQL statement to execute.
   * @param array|false $binds (optional) An array of binding data.
   * @param bool $returnObject (optional) Whether to return a result object or not.
   * @return mixed true for successful "write-type" queries, CI_DB_result instance (method chaining) on "query" success, false on failure.
   */
  public function query($sql, $binds=false, $returnObject=null) {
    $result = parent::query($sql, $binds, $returnObject);
    if ($result === false) {
      $error = parent::error();
      throw new \RuntimeException($error['message'], $error['code']);
    }
    return $result;
  }

  /**
   * Load the result drivers.
   * @return string the name of the result class.
   */
  public function load_rdriver(): string {
    $driver = '\X\Database\\' . ucfirst($this->dbdriver) . 'Driver';
    if ( ! class_exists($driver, false)) {
      require_once(BASEPATH.'database/DB_result.php');
      require_once(BASEPATH.'database/drivers/'.$this->dbdriver.'/'.$this->dbdriver.'_result.php');
      eval('namespace X\Database {class ' . ucfirst($this->dbdriver) . 'Driver extends \X\Database\Driver\\' . ucfirst($this->dbdriver) . '\Result {use \X\Database\Result;}}');
    }
    return $driver;
  }

  /**
   * Get QB FROM data.
   * @param int $index Index of the table name list specified in the from clause. Default is 0.
   * @return bool QB FROM.
   */
  public function isset_qb_from(int $index=0): bool {
    return isset($this->qb_from[$index]);
  }

  /**
   * Insert on duplicate key update statement.
   * Generates a platform-specific insert string from the supplied data.
   * @param string $table Table name.
   * @param array $keys INSERT keys.
   * @param array $values INSERT values.
   * @return string INSERT query.
   */
  private function _insert_on_duplicate_update(string $table, array $keys, array $values): string {
    foreach ($keys as $key)
      $update_fields[] = $key . '= VALUES(' . $key . ')';
    return 'INSERT INTO ' . $table . ' (' . implode(', ', $keys) . ') VALUES (' . implode(', ', $values) . ') ON DUPLICATE KEY UPDATE ' . implode(', ', $update_fields);
  }

  /**
   * Insert on duplicate key update batch statement.
   * Generates a platform-specific insert string from the supplied data.
   * @param string $table Table name
   * @param array $keys INSERT keys
   * @param array $values INSERT values
   * @return string INSERT ON DUPLICATE KEY UPDATE query.
   */
  private function _insert_on_duplicate_update_batch(string $table, array $keys, array $values): string {
    foreach ($keys as $key)
      $update_fields[] = $key . '= VALUES(' . $key . ')';
    return 'INSERT INTO ' . $table . ' (' . implode(', ', $keys) . ') VALUES ' . implode(', ', $values) . ' ON DUPLICATE KEY UPDATE ' . implode(', ', $update_fields);
  }
}