<?php
/**
 * Base model class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 * @property CI_DB_query_builder $db
 */
namespace X\Model;
use X\Util\Loader;
use X\Util\Logger;

abstract class Model extends \CI_Model {

  /**
   * @var string $table
   */
  const TABLE = '';

  /**
   * @var string|string[] $model
   */
  protected $model;

  /**
   * @var string|string[] $library
   */
  protected $library;

  /**
   * construct
   */
  public function __construct() {
    parent::__construct();
    Loader::model($this->model);
    Loader::library($this->library);
  }

  /**
   * Get database object
   *
   * @param string $config
   * @return CI_DB
   */
  public static function db(string $config = 'default') {
    static $db;
    if (!isset($db[$config])) {
      $db[$config] = Loader::database($config, true);
    }
    return $db[$config];
  }

  /**
   * Get database object
   *
   * @param string $config
   * @return CI_DB
   */
  public static function is_connect(string $config = 'default'): bool {
    $db = Loader::database($config, true);
    $connected = !empty($db->conn_id);
    $db->close();
    return $connected;
  }

  /**
   * Get all
   */
  public function get_all() {
    return $this->get()->result_array();
  }

  /**
   * Get by id
   */
  public function get_by_id(int $id) {
    return $this->where('id', $id)->get()->row_array();
  }

  /**
   * countById
   */
  public function count_by_id(int $id): int {
    return $this->where('id', $id)->count_all_results();
  }

  /**
   * Exists by id
   */
  public function exists_by_id(int $id) {
    $count = $this->countById($id);
    return $count !== 0;
  }

  // ----------------------------------------------------------------
  /**
   * Insert_On_Duplicate_Key_Update
   *
   * Compiles insert strings and runs the queries
   *
   * ```php
   *   $SampleModel
   *     ->set([
   *       'key' => '1',
   *       'title' => 'My title',
   *       'name' => 'My Name'
   *     ])
   *     ->insert_on_duplicate_update();
   *     
   *   // You can also
   *   $SampleModel
   *     ->set('key', '1')
   *     ->set('title', 'My title')
   *     ->set('name', 'My Name')
   *     ->insert_on_duplicate_update();
   * ```
   *
   * @param   string $table = '' Table to insert into
   * @param   array|object $set = null an associative array of insert values
   * @param   bool $escape = null Whether to escape values and identifiers
   * @param   int  $batch_size = 100 Count of rows to insert at once
   * @return  int Insert ID
   */
  public function insert_on_duplicate_update(string $table = '', $set = null, bool $escape = null): int {
    if (\method_exists(self::db(), 'isset_qb_from') && !self::db()->isset_qb_from() && empty($table)) {
      $table = static::TABLE;
    }
    return self::db()->insert_on_duplicate_update($table, $set, $escape);
  }

  /**
   * Insert_On_Duplicate_Key_Update_Batch
   *
   * Compiles batch insert strings and runs the queries
   *
   * ```php
   *   $SampleModel
   *     ->set_insert_batch([
   *       [
   *         'key' => '1',
   *         'title' => 'My title',
   *         'name' => 'My Name'
   *       ],
   *       [
   *         'key' => '2',
   *         'title' => 'Another title',
   *         'name' => 'Another Name'
   *       ]
   *     ])
   *     ->insert_on_duplicate_update_batch();
   * ```
   *
   * @param   string $table = '' Table to insert into
   * @param   array|object $set = null an associative array of insert values
   * @param   bool $escape = null Whether to escape values and identifiers
   * @param   int  $batch_size = 100 Count of rows to insert at once
   * @return  int Number of rows inserted or FALSE on failure
   */
  public function insert_on_duplicate_update_batch(string $table = '', $set = null, bool $escape = null, int $batch_size = 100): int {
    if (\method_exists(self::db(), 'isset_qb_from') && !self::db()->isset_qb_from() && empty($table)) {
      $table = static::TABLE;
    }
    return self::db()->insert_on_duplicate_update_batch($table, $set, $escape, $batch_size);
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
    if (\method_exists(self::db(), 'isset_qb_from') && !self::db()->isset_qb_from() && empty($table)) {
      $table = static::TABLE;
    }
    return self::db()->insert($table, $set, $escape);
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
  public function insert_batch($table, $set = null, $escape = null, $batch_size = 100): array {
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
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
    if (\method_exists(self::db(), 'isset_qb_from') && !self::db()->isset_qb_from() && empty($table)) {
      $table = static::TABLE;
    }
    return self::db()->update($table, $set, $where, $limit);
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
  public function update_batch($table, $set = null, $index = null, $batch_size = 100): int {
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
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
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
  }

  /**
   * Load the result drivers
   *
   * @see \DB_driver::load_rdriver()
   * @return  string the name of the result class
   */
  public function load_rdriver():string {
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
  }

  // ----------------------------------------------------------------
  // Override QueryBuilder method
  /**
   * Select
   *
   * Generates the SELECT portion of the query
   *
   * @param   string
   * @param   mixed
   * @return  CI_DB_query_builder
   */
  public function select($select = '*', $escape = NULL) {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Select Max
   *
   * Generates a SELECT MAX(field) portion of a query
   *
   * @param   string  the field
   * @param   string  an alias
   * @return  CI_DB_query_builder
   */
  public function select_max($select = '', $alias = '') {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Select Min
   *
   * Generates a SELECT MIN(field) portion of a query
   *
   * @param   string  the field
   * @param   string  an alias
   * @return  CI_DB_query_builder
   */
  public function select_min($select = '', $alias = '') {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Select Average
   *
   * Generates a SELECT AVG(field) portion of a query
   *
   * @param   string  the field
   * @param   string  an alias
   * @return  CI_DB_query_builder
   */
  public function select_avg($select = '', $alias = '') {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Select Sum
   *
   * Generates a SELECT SUM(field) portion of a query
   *
   * @param   string  the field
   * @param   string  an alias
   * @return  CI_DB_query_builder
   */
  public function select_sum($select = '', $alias = '') {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * DISTINCT
   *
   * Sets a flag which tells the query string compiler to add DISTINCT
   *
   * @param   bool    $val
   * @return  CI_DB_query_builder
   */
  public function distinct($val = TRUE) {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * From
   *
   * Generates the FROM portion of the query
   *
   * @param   mixed   $from   can be a string or array
   * @return  CI_DB_query_builder
   */
  public function from($from) {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * JOIN
   *
   * Generates the JOIN portion of the query
   *
   * @param   string
   * @param   string  the join condition
   * @param   string  the type of join
   * @param   string  whether not to try to escape identifiers
   * @return  CI_DB_query_builder
   */
  public function join($table, $cond, $type = '', $escape = NULL) {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * WHERE
   *
   * Generates the WHERE portion of the query.
   * Separates multiple calls with 'AND'.
   *
   * @param   mixed
   * @param   mixed
   * @param   bool
   * @return  CI_DB_query_builder
   */
  public function where($key, $value = NULL, $escape = NULL) {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * OR WHERE
   *
   * Generates the WHERE portion of the query.
   * Separates multiple calls with 'OR'.
   *
   * @param   mixed
   * @param   mixed
   * @param   bool
   * @return  CI_DB_query_builder
   */
  public function or_where($key, $value = NULL, $escape = NULL) {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * WHERE IN
   *
   * Generates a WHERE field IN('item', 'item') SQL query,
   * joined with 'AND' if appropriate.
   *
   * @param   string  $key    The field to search
   * @param   array   $values The values searched on
   * @param   bool    $escape
   * @return  CI_DB_query_builder
   */
  public function where_in($key = NULL, $values = NULL, $escape = NULL) {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * OR WHERE IN
   *
   * Generates a WHERE field IN('item', 'item') SQL query,
   * joined with 'OR' if appropriate.
   *
   * @param   string  $key    The field to search
   * @param   array   $values The values searched on
   * @param   bool    $escape
   * @return  CI_DB_query_builder
   */
  public function or_where_in($key = NULL, $values = NULL, $escape = NULL) {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * WHERE NOT IN
   *
   * Generates a WHERE field NOT IN('item', 'item') SQL query,
   * joined with 'AND' if appropriate.
   *
   * @param   string  $key    The field to search
   * @param   array   $values The values searched on
   * @param   bool    $escape
   * @return  CI_DB_query_builder
   */
  public function where_not_in($key = NULL, $values = NULL, $escape = NULL) {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * OR WHERE NOT IN
   *
   * Generates a WHERE field NOT IN('item', 'item') SQL query,
   * joined with 'OR' if appropriate.
   *
   * @param   string  $key    The field to search
   * @param   array   $values The values searched on
   * @param   bool    $escape
   * @return  CI_DB_query_builder
   */
  public function or_where_not_in($key = NULL, $values = NULL, $escape = NULL) {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * LIKE
   *
   * Generates a %LIKE% portion of the query.
   * Separates multiple calls with 'AND'.
   *
   * @param   mixed   $field
   * @param   string  $match
   * @param   string  $side
   * @param   bool    $escape
   * @return  CI_DB_query_builder
   */
  public function like($field, $match = '', $side = 'both', $escape = NULL) {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * NOT LIKE
   *
   * Generates a NOT LIKE portion of the query.
   * Separates multiple calls with 'AND'.
   *
   * @param   mixed   $field
   * @param   string  $match
   * @param   string  $side
   * @param   bool    $escape
   * @return  CI_DB_query_builder
   */
  public function not_like($field, $match = '', $side = 'both', $escape = NULL) {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * OR LIKE
   *
   * Generates a %LIKE% portion of the query.
   * Separates multiple calls with 'OR'.
   *
   * @param   mixed   $field
   * @param   string  $match
   * @param   string  $side
   * @param   bool    $escape
   * @return  CI_DB_query_builder
   */
  public function or_like($field, $match = '', $side = 'both', $escape = NULL) {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * OR NOT LIKE
   *
   * Generates a NOT LIKE portion of the query.
   * Separates multiple calls with 'OR'.
   *
   * @param   mixed   $field
   * @param   string  $match
   * @param   string  $side
   * @param   bool    $escape
   * @return  CI_DB_query_builder
   */
  public function or_not_like($field, $match = '', $side = 'both', $escape = NULL) {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Starts a query group.
   *
   * @param   string  $not    (Internal use only)
   * @param   string  $type   (Internal use only)
   * @return  CI_DB_query_builder
   */
  public function group_start($not = '', $type = 'AND ') {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Starts a query group, but ORs the group
   *
   * @return  CI_DB_query_builder
   */
  public function or_group_start() {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Starts a query group, but NOTs the group
   *
   * @return  CI_DB_query_builder
   */
  public function not_group_start() {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Starts a query group, but OR NOTs the group
   *
   * @return  CI_DB_query_builder
   */
  public function or_not_group_start() {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Ends a query group
   *
   * @return  CI_DB_query_builder
   */
  public function group_end() {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * GROUP BY
   *
   * @param   string  $by
   * @param   bool    $escape
   * @return  CI_DB_query_builder
   */
  public function group_by($by, $escape = NULL) {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * HAVING
   *
   * Separates multiple calls with 'AND'.
   *
   * @param   string  $key
   * @param   string  $value
   * @param   bool    $escape
   * @return  CI_DB_query_builder
   */
  public function having($key, $value = NULL, $escape = NULL) {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * OR HAVING
   *
   * Separates multiple calls with 'OR'.
   *
   * @param   string  $key
   * @param   string  $value
   * @param   bool    $escape
   * @return  CI_DB_query_builder
   */
  public function or_having($key, $value = NULL, $escape = NULL) {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * ORDER BY
   *
   * @param   string  $orderby
   * @param   string  $direction  ASC, DESC or RANDOM
   * @param   bool    $escape
   * @return  CI_DB_query_builder
   */
  public function order_by($orderby, $direction = '', $escape = NULL) {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * LIMIT
   *
   * @param   int $value  LIMIT value
   * @param   int $offset OFFSET value
   * @return  CI_DB_query_builder
   */
  public function limit($value, $offset = 0) {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Sets the OFFSET value
   *
   * @param   int $offset OFFSET value
   * @return  CI_DB_query_builder
   */
  public function offset($offset) {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * The "set" function.
   *
   * Allows key/value pairs to be set for inserting or updating
   *
   * @param   mixed
   * @param   string
   * @param   bool
   * @return  CI_DB_query_builder
   */
  public function set($key, $value = '', $escape = NULL) {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Get SELECT query string
   *
   * Compiles a SELECT query string and returns the sql.
   *
   * @param   string  the table name to select from (optional)
   * @param   bool    TRUE: resets QB values; FALSE: leave QB values alone
   * @return  string
   */
  public function get_compiled_select($table = '', $reset = TRUE) {
    if (\method_exists(self::db(), 'isset_qb_from') && !self::db()->isset_qb_from() && empty($table)) {
      $table = static::TABLE;
    }
    return self::db()->get_compiled_select($table, $reset);
  }

  /**
   * Get
   *
   * Compiles the select statement based on the other functions called
   * and runs the query
   *
   * @param   string  the table
   * @param   string  the limit clause
   * @param   string  the offset clause
   * @return  CI_DB_result
   */
  public function get($table = '', $limit = NULL, $offset = NULL) {
    if (\method_exists(self::db(), 'isset_qb_from') && !self::db()->isset_qb_from() && empty($table)) {
      $table = static::TABLE;
    }
    return self::db()->get($table, $limit, $offset);
  }

  /**
   * "Count All Results" query
   *
   * Generates a platform-specific query string that counts all records
   * returned by an Query Builder query.
   *
   * @param   string
   * @param   bool    the reset clause
   * @return  int
   */
  public function count_all_results($table = '', $reset = TRUE) {
    if (\method_exists(self::db(), 'isset_qb_from') && !self::db()->isset_qb_from() && empty($table)) {
      $table = static::TABLE;
    }
    return self::db()->count_all_results($table, $reset);
  }

  /**
   * Get_Where
   *
   * Allows the where clause, limit and offset to be added directly
   *
   * @param   string  $table
   * @param   string  $where
   * @param   int $limit
   * @param   int $offset
   * @return  CI_DB_result
   */
  public function get_where($table = '', $where = NULL, $limit = NULL, $offset = NULL) {
    if (\method_exists(self::db(), 'isset_qb_from') && !self::db()->isset_qb_from() && empty($table)) {
      $table = static::TABLE;
    }
    return self::db()->get_where($table, $where, $limit, $offset);
  }

  // /**
  //  * Insert_Batch
  //  *
  //  * Compiles batch insert strings and runs the queries
  //  *
  //  * @param    string  $table  Table to insert into
  //  * @param    array   $set    An associative array of insert values
  //  * @param    bool    $escape Whether to escape values and identifiers
  //  * @return   int Number of rows inserted or FALSE on failure
  //  */
  // public function insert_batch($table, $set = NULL, $escape = NULL, $batch_size = 100) {
  // }

  /**
   * The "set_insert_batch" function.  Allows key/value pairs to be set for batch inserts
   *
   * @param   mixed
   * @param   string
   * @param   bool
   * @return  CI_DB_query_builder
   */
  public function set_insert_batch($key, $value = '', $escape = NULL) {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Get INSERT query string
   *
   * Compiles an insert query and returns the sql
   *
   * @param   string  the table to insert into
   * @param   bool    TRUE: reset QB values; FALSE: leave QB values alone
   * @return  string
   */
  public function get_compiled_insert($table = '', $reset = TRUE) {
    if (\method_exists(self::db(), 'isset_qb_from') && !self::db()->isset_qb_from() && empty($table)) {
      $table = static::TABLE;
    }
    return self::db()->get_compiled_insert($table, $reset);
  }

  // /**
  //  * Insert
  //  *
  //  * Compiles an insert string and runs the query
  //  *
  //  * @param    string  the table to insert data into
  //  * @param    array   an associative array of insert values
  //  * @param    bool    $escape Whether to escape values and identifiers
  //  * @return   bool    TRUE on success, FALSE on failure
  //  */
  // public function insert($table = '', $set = NULL, $escape = NULL) {
  // }

  /**
   * Replace
   *
   * Compiles an replace into string and runs the query
   *
   * @param   string  the table to replace data into
   * @param   array   an associative array of insert values
   * @return  bool    TRUE on success, FALSE on failure
   */
  public function replace($table = '', $set = NULL) {
    if (\method_exists(self::db(), 'isset_qb_from') && !self::db()->isset_qb_from() && empty($table)) {
      $table = static::TABLE;
    }
    return self::db()->replace($table, $set);
  }

  /**
   * Get UPDATE query string
   *
   * Compiles an update query and returns the sql
   *
   * @param   string  the table to update
   * @param   bool    TRUE: reset QB values; FALSE: leave QB values alone
   * @return  string
   */
  public function get_compiled_update($table = '', $reset = TRUE) {
    if (\method_exists(self::db(), 'isset_qb_from') && !self::db()->isset_qb_from() && empty($table)) {
      $table = static::TABLE;
    }
    return self::db()->get_compiled_update($table, $reset);
  }

  // /**
  //  * UPDATE
  //  *
  //  * Compiles an update string and runs the query.
  //  *
  //  * @param    string  $table
  //  * @param    array   $set    An associative array of update values
  //  * @param    mixed   $where
  //  * @param    int $limit
  //  * @return   bool    TRUE on success, FALSE on failure
  //  */
  // public function update($table = '', $set = NULL, $where = NULL, $limit = NULL) {
  // }

  // /**
  //  * Update_Batch
  //  *
  //  * Compiles an update string and runs the query
  //  *
  //  * @param    string  the table to retrieve the results from
  //  * @param    array   an associative array of update values
  //  * @param    string  the where key
  //  * @return   int number of rows affected or FALSE on failure
  //  */
  // public function update_batch($table, $set = NULL, $index = NULL, $batch_size = 100) {
  // }

  /**
   * The "set_update_batch" function.  Allows key/value pairs to be set for batch updating
   *
   * @param   array
   * @param   string
   * @param   bool
   * @return  CI_DB_query_builder
   */
  public function set_update_batch($key, $index = '', $escape = NULL) {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Empty Table
   *
   * Compiles a delete string and runs "DELETE FROM table"
   *
   * @param   string  the table to empty
   * @return  bool    TRUE on success, FALSE on failure
   */
  public function empty_table($table = '') {
    if (\method_exists(self::db(), 'isset_qb_from') && !self::db()->isset_qb_from() && empty($table)) {
      $table = static::TABLE;
    }
    return self::db()->empty_table($table);
  }

  /**
   * Truncate
   *
   * Compiles a truncate string and runs the query
   * If the database does not support the truncate() command
   * This function maps to "DELETE FROM table"
   *
   * @param   string  the table to truncate
   * @return  bool    TRUE on success, FALSE on failure
   */
  public function truncate($table = '') {
    if (\method_exists(self::db(), 'isset_qb_from') && !self::db()->isset_qb_from() && empty($table)) {
      $table = static::TABLE;
    }
    return self::db()->truncate($table);
  }

  /**
   * Get DELETE query string
   *
   * Compiles a delete query string and returns the sql
   *
   * @param   string  the table to delete from
   * @param   bool    TRUE: reset QB values; FALSE: leave QB values alone
   * @return  string
   */
  public function get_compiled_delete($table = '', $reset = TRUE) {
    if (\method_exists(self::db(), 'isset_qb_from') && !self::db()->isset_qb_from() && empty($table)) {
      $table = static::TABLE;
    }
    return self::db()->get_compiled_delete($table, $reset);
  }

  /**
   * Delete
   *
   * Compiles a delete string and runs the query
   *
   * @param   mixed   the table(s) to delete from. String or array
   * @param   mixed   the where clause
   * @param   mixed   the limit clause
   * @param   bool
   * @return  mixed
   */
  public function delete($table = '', $where = '', $limit = NULL, $reset_data = TRUE) {
    if (\method_exists(self::db(), 'isset_qb_from') && !self::db()->isset_qb_from() && empty($table)) {
      $table = static::TABLE;
    }
    return self::db()->delete($table, $where, $limit, $reset_data);
  }

  /**
   * DB Prefix
   *
   * Prepends a database prefix if one exists in configuration
   *
   * @param   string  the table
   * @return  string
   */
  public function dbprefix($table = '') {
    if (\method_exists(self::db(), 'isset_qb_from') && !self::db()->isset_qb_from() && empty($table)) {
      $table = static::TABLE;
    }
    return self::db()->dbprefix($table);
  }

  /**
   * Set DB Prefix
   *
   * Set's the DB Prefix to something new without needing to reconnect
   *
   * @param   string  the prefix
   * @return  string
   */
  public function set_dbprefix($prefix = '') {
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
  }

  /**
   * Start Cache
   *
   * Starts QB caching
   *
   * @return  CI_DB_query_builder
   */
  public function start_cache() {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Stop Cache
   *
   * Stops QB caching
   *
   * @return  CI_DB_query_builder
   */
  public function stop_cache() {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Flush Cache
   *
   * Empties the QB cache
   *
   * @return  CI_DB_query_builder
   */
  public function flush_cache() {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Reset Query Builder values.
   *
   * Publicly-visible method to reset the QB values.
   *
   * @return  CI_DB_query_builder
   */
  public function reset_query() {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  // ----------------------------------------------------------------
  // Override CI_DB_driver method
  /**
   * Start Transaction
   *
   * @param   bool    $test_mode = FALSE
   * @return  bool
   */
  public function trans_start($test_mode = FALSE) {
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
  }

  /**
   * Begin Transaction
   *
   * @param   bool    $test_mode
   * @return  bool
   */
  public function trans_begin($test_mode = FALSE) {
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
  }

  /**
   * Complete Transaction
   *
   * @return  bool
   */
  public function trans_complete() {
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
  }

  /**
   * Lets you retrieve the transaction flag to determine if it has failed
   *
   * @return  bool
   */
  public function trans_status() {
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
  }

  /**
   * Commit Transaction
   *
   * @return  bool
   */
  public function trans_commit() {
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
  }

  /**
   * Rollback Transaction
   *
   * @return  bool
   */
  public function trans_rollback() {
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
  }

  /**
   * Set foreign key check
   *
   * @return  bool
   */
  public function set_foreign_key_checks(bool $toCheck) {
    return $this->query('SET foreign_key_checks=' . $toCheck ? 1 : 0);
  }

  /**
   * Returns the last query that was executed
   *
   * @return  string
   */
  public function last_query() {
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
  }

  /**
   * "Smart" Escape String
   *
   * Escapes data based on type
   * Sets boolean and null types
   *
   * @param   string
   * @return  mixed
   */
  public function escape($str) {
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
  }

  /**
   * Escape String
   *
   * @param   string|string[] $str    Input string
   * @param   bool    $like   Whether or not the string will be used in a LIKE condition
   * @return  string
   */
  public function escape_str($str, $like = FALSE) {
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
  }

  /**
   * Escape LIKE String
   *
   * Calls the individual driver for platform
   * specific escaping for LIKE conditions
   *
   * @param   string|string[]
   * @return  mixed
   */
  public function escape_like_str($str) {
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
  }

  /**
   * Primary
   *
   * Retrieves the primary key. It assumes that the row in the first
   * position is the primary key
   *
   * @param   string  $table  Table name
   * @return  string
   */
  public function primary($table = null) {
    if (empty($table)) {
      $table = static::TABLE;
    }
    return self::db()->primary($table);
  }

  /**
   * "Count All" query
   *
   * Generates a platform-specific query string that counts all records in
   * the specified database
   *
   * @param   string
   * @return  int
   */
  public function count_all($table = '') {
    if (empty($table)) {
      $table = static::TABLE;
    }
    return self::db()->count_all($table);
  }

  /**
   * Returns an array of table names
   *
   * @param   string  $constrain_by_prefix = FALSE
   * @return  array
   */
  public function list_tables($constrain_by_prefix = FALSE) {
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
  }

  /**
   * Determine if a particular table exists
   *
   * @param   string  $table
   * @return  bool
   */
  public function table_exists($table = null) {
    if (empty($table)) {
      $table = static::TABLE;
    }
    return self::db()->table_exists($table);
  }

  /**
   * Fetch Field Names
   *
   * @param   string  $table  Table name
   * @return  array
   */
  public function list_fields($table = null) {
    if (empty($table)) {
      $table = static::TABLE;
    }
    return self::db()->list_fields($table);
  }

  /**
   * Determine if a particular field exists
   *
   * @param   string
   * @param   string
   * @return  bool
   */
  public function field_exists($field_name, $table = null) {
    if (empty($table)) {
      $table = static::TABLE;
    }
    return self::db()->field_exists($field_name, $table);
  }

  /**
   * Returns an object with field data
   *
   * @param   string  $table  the table name
   * @return  array
   */
  public function field_data($table = null) {
     if (empty($table)) {
      $table = static::TABLE;
    }
    return self::db()->field_data($table);
  }

  /**
   * Last error
   *
   * @return  array
   */
  public function error() {
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
  }

  /**
   * Insert ID
   *
   * @return  int
   */
  public function insert_id() {
    return call_user_func_array([self::insert_id(), __FUNCTION__], func_get_args());
  }

  /**
   * Enable Query Caching
   *
   * @return  void
   */
  public function cache_on() {
    self::db()->cache_on();
  }

  /**
   * Disable Query Caching.
   *
   * @return  void
   */
  public function cache_off() {
    self::db()->cache_off();
  }

  /**
   * Delete the cache files associated with a particular URI
   *
   * @param string  $segment_one First URI segment.
   * @param string  $segment_two Second URI segment.
   * @return  bool
   */
  public function cache_delete(string $segment_one = '', string  $segment_two = '') {
    self::db()->cache_delete($segment_one, $segment_two);
  }

  /**
   * Delete All cache files.
   *
   * @return  bool
   */
  public function cache_delete_all() {
    self::db()->cache_delete_all();
  }
}