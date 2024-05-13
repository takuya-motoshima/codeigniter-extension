<?php
namespace X\Model;
use \X\Util\Loader;

/**
 * CI_Model extension.
 */
#[\AllowDynamicProperties]
abstract class Model extends \CI_Model {
  /**
   * Table name.
   * @var string
   */
  const TABLE = '';

  /**
   * Auto-loading model name.
   * @var string|string[]
   */
  protected $model;

  /**
   * Auto-loading library name.
   * @var string|string[]
   */
  protected $library;

  /**
   * Initialize Model.
   */
  public function __construct() {
    parent::__construct();
    Loader::model($this->model);
    Loader::library($this->library);
  }

  /**
   * Get database object.
   * @param string $config Connection group name. Default is "default".
   * @return CI_DB CI_DB instance.
   */
  public static function db(string $config='default') {
    static $db;
    if (!isset($db[$config]))
      $db[$config] = Loader::database($config, true);
    return $db[$config];
  }

  /**
   * DB connection check
   * @param string $config Connection group name. Default is "default".
   * @return bool Whether you could connect to DB or not.
   */
  public static function is_connect(string $config='default'): bool {
    $db = Loader::database($config, true);
    $connected = !empty($db->conn_id);
    $db->close();
    return $connected;
  }

  /**
   * Query result. "array" version.
   * @return array Search result data.
   */
  public function get_all() {
    return $this->get()->result_array();
  }

  /**
   * Find records matching the ID.
   * @param int $id ID.
   * @return array Search result data.
   */
  public function get_by_id(int $id) {
    return $this->where('id', $id)->get()->row_array();
  }

  /**
   * Get counts matching ID.
   * @param int $id ID.
   * @return int Search result count.
   */
  public function count_by_id(int $id): int {
    return $this->where('id', $id)->count_all_results();
  }

  /**
   * Check if the ID exists.
   * @param int $id ID.
   * @return bool Whether the ID exists.
   */
  public function exists_by_id(int $id): bool {
    $count = $this->count_by_id($id);
    return $count !== 0;
  }

  // ----------------------------------------------------------------
  /**
   * Insert_On_Duplicate_Key_Update.
   * ```php
   * $SampleModel
   *   ->set([
   *     'key' => '1',
   *     'title' => 'My title',
   *     'name' => 'My Name'
   *   ])
   *   ->insert_on_duplicate_update();
   * $SampleModel
   *   ->set('key', '1')
   *   ->set('title', 'My title')
   *   ->set('name', 'My Name')
   *   ->insert_on_duplicate_update();
   * ```
   * @param string $table (optional) Table name.
   * @param array|object $set (optional) an associative array of insert values.
   * @param bool $escape (optional) Whether to escape values and identifiers.
   * @return int Insert ID.
   */
  public function insert_on_duplicate_update(string $table='', $set=null, bool $escape=null): int {
    if (\method_exists(self::db(), 'isset_qb_from') && !self::db()->isset_qb_from() && empty($table))
      $table = static::TABLE;
    return self::db()->insert_on_duplicate_update($table, $set, $escape);
  }

  /**
   * Insert_On_Duplicate_Key_Update_Batch.
   * ```php
   * $SampleModel
   *   ->set_insert_batch([
   *     ['key' => '1', 'title' => 'My title', 'name' => 'My Name'],
   *     ['key' => '2', 'title' => 'Another title', 'name' => 'Another Name']
   *   ])
   *   ->insert_on_duplicate_update_batch();
   * ```
   * @param string $table (optional) Table name.
   * @param array|object $set (optional) an associative array of insert values.
   * @param bool $escape (optional) Whether to escape values and identifiers.
   * @param int $batchSize (optional) Count of rows to insert at once. Default is 100.
   * @return int Number of rows inserted or false on failure.
   */
  public function insert_on_duplicate_update_batch(string $table='', $set=null, bool $escape=null, int $batchSize=100): int {
    if (\method_exists(self::db(), 'isset_qb_from') && !self::db()->isset_qb_from() && empty($table))
      $table = static::TABLE;
    return self::db()->insert_on_duplicate_update_batch($table, $set, $escape, $batchSize);
  }

  /**
   * Insert.
   * @param string $table (optional) Table name.
   * @param array|object $set (optional) An associative array of field/value pairs.
   * @param bool $escape (optional) Whether to escape values and identifiers.
   * @return int Insert ID.
   */
  public function insert($table='', $set=null, $escape=null): int {
    if (\method_exists(self::db(), 'isset_qb_from') && !self::db()->isset_qb_from() && empty($table))
      $table = static::TABLE;
    return self::db()->insert($table, $set, $escape);
  }

  /**
   * Insert_Batch.
   * @param string $table Table name.
   * @param array|object $set (optional) Data to insert.
   * @param bool $escape (optional) Whether to escape values and identifiers.
   * @param int $batchSize (optional) Count of rows to insert at once. Default is 100.
   * @return int[] Insert ID.
   */
  public function insert_batch($table, $set=null, $escape=null, $batchSize=100): array {
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
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
    if (\method_exists(self::db(), 'isset_qb_from') && !self::db()->isset_qb_from() && empty($table))
      $table = static::TABLE;
    self::db()->update($table, $set, $where, $limit);
  }

  /**
   * Update_Batch.
   * @param string $table Table name.
   * @param array|object $set (optional) Field name, or an associative array of field/value pairs.
   * @param string $value (optional)  Field value, if $set is a single field.
   * @param int $batchSize (optional) Count of rows to update at once. Default is 100.
   * @return int Number of rows updated or FALSE on failure
   */
  public function update_batch($table, $set=null, $value=null, $batchSize=100): int {
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
  }

  /**
   * Execute the query.
   * @param string $sql The SQL statement to execute.
   * @param array|false $binds (optional) An array of binding data.
   * @param bool $returnObject (optional) Whether to return a result object or not.
   * @return mixed true for successful "write-type" queries, CI_DB_result instance (method chaining) on "query" success, false on failure.
   */
  public function query($sql, $binds=false, $returnObject=null) {
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
  }

  /**
   * Load the result drivers.
   * @return string the name of the result class.
   */
  public function load_rdriver(): string {
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
  }

  // ----------------------------------------------------------------
  // Override QueryBuilder method
  /**
   * Generates the SELECT portion of the query.
   * @param string $select (optional) The SELECT portion of a query.
   * @param bool $escape (optional) Whether to escape values and identifiers.
   * @return Model
   */
  public function select($select='*', $escape=null): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Generates a SELECT MAX(field) portion of a query.
   * @param string $select (optional)  Field to compute the maximum of.
   * @param string $alias (optional) Alias for the resulting value name.
   * @return Model
   */
  public function select_max($select='', $alias=''): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Generates a SELECT MIN(field) portion of a query
   * @param string $select (optional) Field to compute the minimum of.
   * @param string $alias (optional) Alias for the resulting value name.
   * @return Model
   */
  public function select_min($select='', $alias=''): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Generates a SELECT AVG(field) portion of a query
   * @param string $select (optional) Field to compute the average of.
   * @param string $alias (optional) Alias for the resulting value name.
   * @return Model
   */
  public function select_avg($select='', $alias=''): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Generates a SELECT SUM(field) portion of a query
   * @param string $select (optional) Field to compute the sum of.
   * @param string $alias (optional) Alias for the resulting value name.
   * @return Model
   */
  public function select_sum($select='', $alias=''): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Sets a flag which tells the query string compiler to add DISTINCT.
   * @param bool $val Desired value of the "distinct" flag.
   * @return Model
   */
  public function distinct($val=true): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Generates the FROM portion of the query
   * @param mixed $from Table name(s); string or array.
   * @return Model
   */
  public function from($from): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Generates the JOIN portion of the query
   * @param string $table Table name.
   * @param string $cond The JOIN ON condition.
   * @param string $type (optional) The JOIN type.
   * @param bool $escape (optional) Whether to escape values and identifiers.
   * @return Model
   */
  public function join($table, $cond, $type='', $escape=null): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Generates the WHERE portion of the query. Separates multiple calls with 'AND'.
   * @param string $key Name of field to compare, or associative array.
   * @param mixed $value (optional) If a single key, compared to this value.
   * @param bool $escape (optional) Whether to escape values and identifiers.
   * @return Model
   */
  public function where($key, $value=null, $escape=null): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Generates the WHERE portion of the query. Separates multiple calls with 'OR'.
   * @param string $key  Name of field to compare, or associative array.
   * @param mixed $value (optional) If a single key, compared to this value.
   * @param bool $escape (optional) Whether to escape values and identifiers.
   * @return CI_DB_query_builder
   */
  public function or_where($key, $value=null, $escape=null): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Generates a WHERE field IN('item', 'item') SQL query, joined with 'AND' if appropriate.
   * @param string $key (optional) The field to search.
   * @param array $values (optional) The values searched on.
   * @param bool $escape (optional) Whether to escape values and identifiers.
   * @return Model
   */
  public function where_in($key=null, $values=null, $escape=null): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Generates a WHERE field IN('item', 'item') SQL query, joined with 'OR' if appropriate.
   * @param string $key (optional) The field to search.
   * @param array $values (optional) The values searched on.
   * @param bool $escape (optional) Whether to escape values and identifiers.
   * @return Model
   */
  public function or_where_in($key=null, $values=null, $escape=null): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Generates a WHERE field NOT IN('item', 'item') SQL query, joined with 'AND' if appropriate.
   * @param string $key (optional) The field to search.
   * @param array $values (optional) The values searched on.
   * @param bool $escape (optional) Whether to escape values and identifiers.
   * @return Model
   */
  public function where_not_in($key=null, $values=null, $escape=null): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Generates a WHERE field NOT IN('item', 'item') SQL query, joined with 'OR' if appropriate.
   * @param string $key (optional) The field to search.
   * @param array $values (optional) The values searched on.
   * @param bool $escape (optional) Whether to escape values and identifiers.
   * @return Model
   */
  public function or_where_not_in($key=null, $values=null, $escape=null): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Generates a %LIKE% portion of the query. Separates multiple calls with 'AND'.
   * @param mixed $field Field name.
   * @param string $match (optional) Text portion to match.
   * @param string $side (optional) Which side of the expression to put the ‘%’ wildcard on.
   * @param bool $escape (optional) Whether to escape values and identifiers.
   * @return Model
   */
  public function like($field, $match='', $side='both', $escape=null): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Generates a NOT LIKE portion of the query. Separates multiple calls with 'AND'.
   * @param mixed $field Field name.
   * @param string $match (optional) Text portion to match.
   * @param string $side (optional) Which side of the expression to put the ‘%’ wildcard on.
   * @param bool $escape (optional) Whether to escape values and identifiers.
   * @return Model
   */
  public function not_like($field, $match='', $side='both', $escape=null): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Generates a %LIKE% portion of the query. Separates multiple calls with 'OR'.
   * @param mixed $field Field name.
   * @param string $match (optional) Text portion to match.
   * @param string $side (optional) Which side of the expression to put the ‘%’ wildcard on.
   * @param bool $escape (optional) Whether to escape values and identifiers.
   * @return Model
   */
  public function or_like($field, $match='', $side='both', $escape=null): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Generates a NOT LIKE portion of the query. Separates multiple calls with 'OR'.
   * @param mixed $field Field name.
   * @param string $match (optional) Text portion to match.
   * @param string $side (optional) Which side of the expression to put the ‘%’ wildcard on.
   * @param bool $escape (optional) Whether to escape values and identifiers.
   * @return Model
   */
  public function or_not_like($field, $match='', $side='both', $escape=null): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Starts a query group.
   * @param string $not (Internal use only).
   * @param string $type (Internal use only).
   * @return Model
   */
  public function group_start($not='', $type='AND '): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Starts a query group, but ORs the group.
   * @return Model
   */
  public function or_group_start(): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Starts a query group, but NOTs the group.
   * @return Model
   */
  public function not_group_start(): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Starts a query group, but OR NOTs the group.
   * @return Model
   */
  public function or_not_group_start(): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Ends a query group.
   * @return Model
   */
  public function group_end(): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * GROUP BY.
   * @param string $by Field(s) to group by; string or array.
   * @param bool $escape (optional) Whether to escape values and identifiers.
   * @return Model
   */
  public function group_by($by, $escape=null): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * HAVING. Separates multiple calls with 'AND'.
   * @param string $key Identifier (string) or associative array of field/value pairs.
   * @param string $value Value sought if $key is an identifier.
   * @param bool $escape (optional) Whether to escape values and identifiers.
   * @return Model
   */
  public function having($key, $value=null, $escape=null): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * OR HAVING. Separates multiple calls with 'OR'.
   * @param string $key Identifier (string) or associative array of field/value pairs.
   * @param string $value Value sought if $key is an identifier.
   * @param bool $escape (optional) Whether to escape values and identifiers.
   * @return Model
   */
  public function or_having($key, $value=null, $escape=null): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * ORDER BY.
   * @param string $orderby Field to order by.
   * @param string $direction The order requested - ASC, DESC or random.
   * @param bool $escape (optional) Whether to escape values and identifiers.
   * @return Model
   */
  public function order_by($orderby, $direction='', $escape=null): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * LIMIT.
   * @param int $value Number of rows to limit the results to.
   * @param int $offset Number of rows to skip.
   * @return Model
   */
  public function limit($value, $offset=0): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Sets the OFFSET value.
   * @param int $offset Number of rows to skip.
   * @return Model
   */
  public function offset($offset): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Allows key/value pairs to be set for inserting or updating
   * @param mixed $key Field name, or an array of field/value pairs.
   * @param string $value (optional) Field value, if $key is a single field.
   * @param bool $escape (optional) Whether to escape values and identifiers.
   * @return Model
   */
  public function set($key, $value='', $escape=null): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Compiles a SELECT statement and returns it as a string.
   * @param string $table (optional) Table name.
   * @param bool $reset (optional) Whether to reset the current QB values or not.
   * @return string The compiled SQL statement as a string.
   */
  public function get_compiled_select($table='', $reset=true) {
    if (\method_exists(self::db(), 'isset_qb_from') && !self::db()->isset_qb_from() && empty($table))
      $table = static::TABLE;
    return self::db()->get_compiled_select($table, $reset);
  }

  /**
   * Compiles and runs SELECT statement based on the already called Query Builder methods.
   * @param string $table (optional) The table to query.
   * @param string $limit (optional) The LIMIT clause.
   * @param string $offset (optional) The OFFSET clause.
   * @return CI_DB_result
   */
  public function get($table='', $limit=null, $offset=null) {
    if (\method_exists(self::db(), 'isset_qb_from') && !self::db()->isset_qb_from() && empty($table))
      $table = static::TABLE;
    return self::db()->get($table, $limit, $offset);
  }

  /**
   * "Count All Results" query.
   * Generates a platform-specific query string that counts all records
   * returned by an Query Builder query.
   * @param string $table (optional) Table name.
   * @param bool $reset Whether to reset values for SELECTs.
   * @return int
   */
  public function count_all_results($table='', $reset=true) {
    if (\method_exists(self::db(), 'isset_qb_from') && !self::db()->isset_qb_from() && empty($table))
      $table = static::TABLE;
    return self::db()->count_all_results($table, $reset);
  }

  /**
   * Allows the where clause, limit and offset to be added directly.
   * @param string $table (optional) The table(s) to fetch data from; string or array.
   * @param string $where (optional) The WHERE clause.
   * @param int $limit (optional) The LIMIT clause.
   * @param int $offset (optional) The OFFSET clause.
   * @return CI_DB_result
   */
  public function get_where($table='', $where=null, $limit=null, $offset=null) {
    if (\method_exists(self::db(), 'isset_qb_from') && !self::db()->isset_qb_from() && empty($table))
      $table = static::TABLE;
    return self::db()->get_where($table, $where, $limit, $offset);
  }

  /**
   * The "set_insert_batch" function. Allows key/value pairs to be set for batch inserts.
   * @param mixed $key Field name or an array of field/value pairs.
   * @param string $value (optional) Field value, if $key is a single field.
   * @param bool $escape (optional) Whether to escape values and identifiers.
   * @return Model
   */
  public function set_insert_batch($key, $value='', $escape=null): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Get INSERT query string.
   * @param string $table (optional) Table name.
   * @param bool $reset (optional) Whether to reset the current QB values or not.
   * @return string Compiles an INSERT statement and returns it as a string.
   */
  public function get_compiled_insert($table='', $reset=true) {
    if (\method_exists(self::db(), 'isset_qb_from') && !self::db()->isset_qb_from() && empty($table))
      $table = static::TABLE;
    return self::db()->get_compiled_insert($table, $reset);
  }

  /**
   * Replace.
   * @param string $table (optional) Table name.
   * @param array|null $set (optional) An associative array of field/value pairs.
   * @return bool true on success, false on failure.
   */
  public function replace($table='', $set=null) {
    if (\method_exists(self::db(), 'isset_qb_from') && !self::db()->isset_qb_from() && empty($table))
      $table = static::TABLE;
    return self::db()->replace($table, $set);
  }

  /**
   * Get UPDATE query string
   * @param string $table (optional) Table name.
   * @param bool $reset (optional) Whether to reset the current QB values or not.
   * @return string The compiled SQL statement as a string.
   */
  public function get_compiled_update($table='', $reset=true) {
    if (\method_exists(self::db(), 'isset_qb_from') && !self::db()->isset_qb_from() && empty($table))
      $table = static::TABLE;
    return self::db()->get_compiled_update($table, $reset);
  }

  /**
   * The "set_update_batch" function.  Allows key/value pairs to be set for batch updating
   * @param array $key Field name or an array of field/value pairs.
   * @param string $value (optional) Field value, if $key is a single field.
   * @param bool $escape (optional) Whether to escape values and identifiers.
   * @return Model
   */
  public function set_update_batch($key, $value='', $escape=null): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Empty Table.
   * @param string $table (optional) Table name.
   * @return bool true on success, false on failure.
   */
  public function empty_table($table='') {
    if (\method_exists(self::db(), 'isset_qb_from') && !self::db()->isset_qb_from() && empty($table)) {
      $table = static::TABLE;
    }
    return self::db()->empty_table($table);
  }

  /**
   * Truncate.
   * @param string $table (optional) Table name.
   * @return bool true on success, false on failure.
   */
  public function truncate($table='') {
    if (\method_exists(self::db(), 'isset_qb_from') && !self::db()->isset_qb_from() && empty($table))
      $table = static::TABLE;
    return self::db()->truncate($table);
  }

  /**
   * Compiles a DELETE statement and returns it as a string.
   * @param string $table (optional) Table name.
   * @param bool $reset (optional) Whether to reset the current QB values or not.
   * @return string The compiled SQL statement as a string.
   */
  public function get_compiled_delete($table='', $reset=true) {
    if (\method_exists(self::db(), 'isset_qb_from') && !self::db()->isset_qb_from() && empty($table))
      $table = static::TABLE;
    return self::db()->get_compiled_delete($table, $reset);
  }

  /**
   * Delete.
   * @param string $table (optional) The table(s) to delete from; string or array.
   * @param string $where (optional) The WHERE clause.
   * @param int $limit The (optional) LIMIT clause.
   * @param bool $reset (optional) TRUE to reset the query "write" clause.
   * @return CI_DB_query_builder instance (method chaining) or FALSE on failure.
   */
  public function delete($table='', $where='', $limit=null, $reset=true) {
    if (\method_exists(self::db(), 'isset_qb_from') && !self::db()->isset_qb_from() && empty($table))
      $table = static::TABLE;
    return self::db()->delete($table, $where, $limit, $reset);
  }

  /**
   * DB Prefix. Prepends a database prefix if one exists in configuration
   * @param string $table (optional) The table name to prefix.
   * @return string The prefixed table name.
   */
  public function dbprefix($table='') {
    if (\method_exists(self::db(), 'isset_qb_from') && !self::db()->isset_qb_from() && empty($table))
      $table = static::TABLE;
    return self::db()->dbprefix($table);
  }

  /**
   * Set's the DB Prefix to something new without needing to reconnect
   * @param string $prefix The new prefix to use.
   * @return string The DB prefix in use.
   */
  public function set_dbprefix($prefix='') {
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
  }

  /**
   * Starts QB caching.
   * @return Model
   */
  public function start_cache(): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Stops QB caching
   * @return Model
   */
  public function stop_cache(): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Flush Cache.
   * Empties the QB cache.
   * @return Model
   */
  public function flush_cache(): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  /**
   * Reset Query Builder values.
   * Publicly-visible method to reset the QB values.
   * @return Model
   */
  public function reset_query(): Model {
    call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    return $this;
  }

  // ----------------------------------------------------------------
  // Override CI_DB_driver method
  /**
   * Start Transaction.
   * @param bool $testMode (optional) Test mode flag.
   * @return bool TRUE on success, FALSE on failure.
   */
  public function trans_start($testMode=false) {
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
  }

  /**
   * Begin Transaction.
   * @param bool $testMode (optional) Test mode flag.
   * @return bool TRUE on success, FALSE on failure.
   */
  public function trans_begin($testMode=false) {
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
  }

  /**
   * Complete Transaction.
   * @return bool TRUE on success, FALSE on failure.
   */
  public function trans_complete() {
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
  }

  /**
   * Lets you retrieve the transaction flag to determine if it has failed.
   * @return bool TRUE if the transaction succeeded, FALSE if it failed.
   */
  public function trans_status() {
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
  }

  /**
   * Commit Transaction.
   * @return bool TRUE on success, FALSE on failure.
   */
  public function trans_commit() {
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
  }

  /**
   * Rollback Transaction.
   * @return bool TRUE on success, FALSE on failure.
   */
  public function trans_rollback() {
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
  }

  /**
   * Set foreign key check.
   * @param bool $enabled Value of foreign_key_checks.
   * @return bool TRUE on success, FALSE on failure.
   */
  public function set_foreign_key_checks(bool $enabled) {
    return $this->query('SET foreign_key_checks=' . $enabled ? 1 : 0);
  }

  /**
   * Returns the last query that was executed.
   * @return string The last query executed.
   */
  public function last_query() {
    $query = call_user_func_array([self::db(), __FUNCTION__], func_get_args());
    if (empty($query))
      return $query;
    return str_replace(["\n", "\r\n", "\r"], ' ', $query);
  }

  /**
   * Escapes input data based on type, including boolean and NULLs.
   * @param mixed $str The value to escape, or an array of multiple ones.
   * @return mixed The escaped value(s).
   */
  public function escape($str) {
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
  }

  /**
   * Escapes string values.
   * @param string|string[] $str A string value or array of multiple ones.
   * @param bool $like (optional) Whether or not the string will be used in a LIKE condition.
   * @return string The escaped string(s).
   */
  public function escape_str($str, $like=false) {
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
  }

  /**
   * Escape LIKE strings.
   * @param string|string[] $str A string value or array of multiple ones.
   * @return mixed The escaped string(s).
   */
  public function escape_like_str($str) {
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
  }

  /**
   * Retrieves the primary key of a table.
   * @param string $table (optional) Table name.
   * @return string The primary key name, FALSE if none.
   */
  public function primary($table=null) {
    if (empty($table))
      $table = static::TABLE;
    return self::db()->primary($table);
  }

  /**
   * Returns the total number of rows in a table, or 0 if no table was provided.
   * @param string $table (optional) Table name.
   * @return int Row count for the specified table.
   */
  public function count_all($table='') {
    if (empty($table))
      $table = static::TABLE;
    return self::db()->count_all($table);
  }

  /**
   * Gets a list of the tables in the current database.
   * @param string $constrainByPrefix (optional) TRUE to match table names by the configured dbprefix.
   * @return array Array of table names or FALSE on failure.
   */
  public function list_tables($constrainByPrefix=false) {
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
  }

  /**
   * Determine if a particular table exists.
   * @param string $table (optional) The table name.
   * @return bool TRUE if that table exists, FALSE if not.
   */
  public function table_exists($table=null) {
    if (empty($table))
      $table = static::TABLE;
    return self::db()->table_exists($table);
  }

  /**
   * Gets a list of the field names in a table.
   * @param string $table (optional) The table name.
   * @return array Array of field names or FALSE on failure.
   */
  public function list_fields($table=null) {
    if (empty($table))
      $table = static::TABLE;
    return self::db()->list_fields($table);
  }

  /**
   * Determine if a particular field exists.
   * @param string $field The field name.
   * @param string $table (optional) The table name.
   * @return bool TRUE if that field exists in that table, FALSE if not
   */
  public function field_exists($field, $table=null) {
    if (empty($table))
      $table = static::TABLE;
    return self::db()->field_exists($field, $table);
  }

  /**
   * Gets a list containing field data about a table.
   * @param string $table (optional) The table name.
   * @return array Array of field data items or FALSE on failure.
   */
  public function field_data($table=null) {
    if (empty($table))
      $table = static::TABLE;
    return self::db()->field_data($table);
  }

  /**
   * Last error.
   * @return array{code: string|null, message: string|null} Error data.
   */
  public function error() {
    return call_user_func_array([self::db(), __FUNCTION__], func_get_args());
  }

  /**
   * Insert ID.
   * @return int Insert ID.
   */
  public function insert_id() {
    return call_user_func_array([self::insert_id(), __FUNCTION__], func_get_args());
  }

  /**
   * Enable Query Caching.
   * @return void
   */
  public function cache_on() {
    self::db()->cache_on();
  }

  /**
   * Disable Query Caching.
   * @return void
   */
  public function cache_off() {
    self::db()->cache_off();
  }

  /**
   * Delete the cache files associated with a particular URI
   * @param string $segmentOne First URI segment.
   * @param string $segmentTwo Second URI segment.
   * @return void
   */
  public function cache_delete(string $segmentOne='', string  $segmentTwo='') {
    self::db()->cache_delete($segmentOne, $segmentTwo);
  }

  /**
   * Delete All cache files.
   * @return void
   */
  public function cache_delete_all() {
    self::db()->cache_delete_all();
  }
}