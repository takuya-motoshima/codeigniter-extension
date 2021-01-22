<?php
/**
 * Query builder class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Database;

abstract class QueryBuilder extends \CI_DB_query_builder {

  /**
   * @var integer The current page we are on
   */
  protected $qb_cur_page = 1;

  /**
   * @var integer The number of rows/items per page
   */
  protected $qb_rows_per_page  = 10;

  /**
   * @var integer Only show this many page numbers per page
   */
  protected $qb_page_limit     = 10;

  /**
   * @var integer Max pages for this query
   */
  protected $qb_max_pages      = 0;

  /**
   * @var integer Total rows
   */
  protected $qb_total_rows = 0;


  /**
   * Query debug flag
   *
   * Whether to output the query to the log
   *
   * @var bool
   */
  // public $query_debug  = true;

  /**
   * construct
   *
   * @param   string|string[] $config
   */
  public function __construct($config) {
    parent::__construct($config);
  }

  /**
   * Insert_On_Duplicate_Key_Update
   *
   * Compiles an insert string and runs the query
   *
   * @see CI_DB_query_builder::insert()
   * @throws RuntimeException
   * @param   string $table = '' the table to insert data into
   * @param   array|object $set = null an associative array of insert values
   * @param   bool $escape = null Whether to escape values and identifiers
   * @return  int Insert ID
   */
  public function insert_on_duplicate_update($table = '', $set = null, $escape = null): int {
    if ($set !== null) {
      parent::set($set, '', $escape);
    }
    if (count($this->qb_set) === 0) {
      // No valid data array. Folds in cases where keys and values did not match up
      return ($this->db_debug) ? parent::display_error('db_must_use_set') : false;
    }
    if ($table === '') {
      if (!isset($this->qb_from[0])) {
        return ($this->db_debug) ? parent::display_error('db_must_set_table') : false;
      }
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
   * Insert on duplicate key update  statement
   *
   * Generates a platform-specific insert string from the supplied data
   *
   * @param   string $table Table name
   * @param   array $keys INSERT keys
   * @param   array $values INSERT values
   * @return  string
   */
  private function _insert_on_duplicate_update(string $table, array $keys, array $values): string {
    foreach ($keys as $key) {
      $update_fields[] = $key . '= VALUES(' . $key . ')';
    }
    return 'INSERT INTO ' . $table . ' (' . implode(', ', $keys) . ') VALUES (' . implode(', ', $values) . ') ON DUPLICATE KEY UPDATE ' . implode(', ', $update_fields);
  }


  /**
   * Insert_On_Duplicate_Key_Update_Batch
   *
   * Compiles batch insert strings and runs the queries
   *
   * @param   string $table = '' Table to insert into
   * @param   array|object $set = null an associative array of insert values
   * @param   bool $escape = null Whether to escape values and identifiers
   * @param   int  $batch_size = 100 Count of rows to insert at once
   * @return  int Number of rows inserted or FALSE on failure
   */
  public function insert_on_duplicate_update_batch(string $table = '', $set = null, bool $escape = null, int $batch_size = 100): int {
    if ($set !== null) {
      parent::set_insert_batch($set, '', $escape);
    }
    if (count($this->qb_set) === 0) {
      // No valid data array. Folds in cases where keys and values did not match up
      return ($this->db_debug) ? parent::display_error('db_must_use_set') : false;
    }
    if ($table === '') {
      if (!isset($this->qb_from[0])) {
        return ($this->db_debug) ? parent::display_error('db_must_set_table') : false;
      }
      $table = $this->qb_from[0];
    }

    // Batch this baby
    $affected_rows = 0;
    for ($i = 0, $total = count($this->qb_set); $i < $total; $i += $batch_size) {
      $sql = $this->_insert_on_duplicate_update_batch(
        parent::protect_identifiers($table, true, $escape, false),
        $this->qb_keys,
        array_slice($this->qb_set, $i, $batch_size)
      );
      $this->query($sql);
      $affected_rows += $this->affected_rows();
      // $affected_rows += parent::affected_rows();
    }
    parent::_reset_write();
    return $affected_rows;
  }

  /**
   * Insert on duplicate key update batch statement
   *
   * Generates a platform-specific insert string from the supplied data
   *
   * @param   string $table Table name
   * @param   array $keys INSERT keys
   * @param   array $values INSERT values
   * @return  string
   */
  private function _insert_on_duplicate_update_batch(string $table, array $keys, array $values): string {
    foreach ($keys as $key) {
      $update_fields[] = $key . '= VALUES(' . $key . ')';
    }
    return 'INSERT INTO ' . $table . ' (' . implode(', ', $keys) . ') VALUES ' . implode(', ', $values) . ' ON DUPLICATE KEY UPDATE ' . implode(', ', $update_fields);
  }

  /**
   * Insert
   *
   * Compiles an insert string and runs the query
   *
   * @see CI_DB_query_builder::insert()
   * @throws RuntimeException
   * @param   string $table = '' the table to insert data into
   * @param   array|object $set = null an associative array of insert values
   * @param   bool $escape = null Whether to escape values and identifiers
   * @return  int Insert ID
   */
  public function insert($table = '', $set = null, $escape = null): int {
    $result = parent::insert($table, $set, $escape);
    if ($result === false) {
      $error = parent::error();
      throw new \RuntimeException($error['message'], $error['code']);
    }
    return (int) $this->insert_id();
  }

  /**
   * Insert_Batch
   *
   * Compiles batch insert strings and runs the queries
   *
   * @see CI_DB_query_builder::insert_batch()
   * @throws RuntimeException
   * @param   string $table Table to insert into
   * @param   array[] $set = null An associative array of insert values
   * @param   bool $escape = null Whether to escape values and identifiers
   * @param   int  $batch_size = 100 Count of rows to insert at once
   * @return  int[] Insert ID
   */
  public function insert_batch($table, $set = null, $escape = null, $batch_size = 100):array {
    if (parent::insert_batch($table, $set, $escape, $batch_size) === false) {
      $error = parent::error();
      throw new \RuntimeException($error['message'], $error['code']);
    }
    $firstId = $this->insert_id();
    return range($firstId, $firstId + count($set) - 1);
  }

  /**
   * UPDATE
   *
   * Compiles an update string and runs the query.
   *
   * @see CI_DB_query_builder::update()
   * @param   string $table = '' the table to retrieve the results from
   * @param   array|object $set = null an associative array of update values
   * @param   string|array $where = null the where key
   * @param   int $limit = null The LIMIT clause
   * @return  void
   */
  public function update($table = '', $set = null, $where = null, $limit = null) {
    if (parent::update($table, $set, $where, $limit) === false) {
      $error = parent::error();
      throw new \RuntimeException($error['message'], $error['code']);
    }
  }

  /**
   * Update_Batch
   *
   * Compiles an update string and runs the query
   *
   * @see CI_DB_query_builder::update_batch()
   * @param   string $table the table to retrieve the results from
   * @param   array $set = null an associative array of update values
   * @param   string $index = null the where key
   * @param   int $batch_size = 100 Count of rows to update at once
   * @return  int number of rows affected
   */
  public function update_batch($table, $set = null, $index = null, $batch_size = 100):int {
    $affectedRows = parent::update_batch($table, $set, $index, $batch_size);
    if ($affectedRows === false) {
      $error = parent::error();
      throw new \RuntimeException($error['message'], $error['code']);
    }
    return $affectedRows;
  }

  /**
   * Execute the query
   *
   * Accepts an SQL string as input and returns a result object upon
   * successful execution of a "read" type query. Returns boolean true
   * upon successful execution of a "write" type query. Returns boolean
   * false upon failure, and if the $db_debug variable is set to true
   * will raise an error.
   *
   * @see  CI_DB_driver::query()
   * @throws RuntimeException
   * @param   string $sql The SQL statement to execute
   * @param   array $binds = false An array of binding data
   * @param   bool $return_object = null Whether to return a result object or not
   * @return  mixed true for successful “write-type” queries, CI_DB_result instance (method chaining) on “query” success, false on failure
   */
  public function query($sql, $binds = false, $return_object = null) {
    $result = parent::query($sql, $binds, $return_object);
    // $this->query_debug === true && log_message('debug', parent::last_query());
    if ($result === false) {
      $error = parent::error();
      throw new \RuntimeException($error['message'], $error['code']);
    }
    return $result;
  }

  /**
   * Load the result drivers
   *
   * @see \DB_driver::load_rdriver()
   * @return  string the name of the result class
   */
  public function load_rdriver():string {
    $driver = '\X\Database\\' . ucfirst($this->dbdriver) . 'Driver';
    if ( ! class_exists($driver, false)) {
      require_once(BASEPATH.'database/DB_result.php');
      require_once(BASEPATH.'database/drivers/'.$this->dbdriver.'/'.$this->dbdriver.'_result.php');
      eval('namespace X\Database {class ' . ucfirst($this->dbdriver) . 'Driver extends \X\Database\Driver\\' . ucfirst($this->dbdriver) . '\Result {use \X\Database\Result;}}');
      // eval('namespace X\Database {class ' . ucfirst($this->dbdriver) . 'Driver extends \CI_DB_' . $this->dbdriver . '_result {use \X\Database\Result;}}');
    }
    return $driver;
  }

  /**
   * Get QB FROM data
   *
   * @return bool
   */
  public function isset_qb_from(int $index = 0): bool {
    return isset($this->qb_from[$index]);
  }


  /**
   * Get paginated search results
   * 
   * @return  array
   */
  public function paginate(int $offset, int $limit): array {
    return [];
  }
}