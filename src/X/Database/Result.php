<?php
namespace X\Database;

/**
 * Query Result Extension.
 */
trait Result {
  /**
   * The result of the select is converted to an object where the specified column name is the key and the search result is the value.
   * ```php
   * $rows = parent
   *   ->select('id,name')
   *   ->from('user')
   *   ->get()
   *   ->result_keyvalue('id');
   * var_export($rows);
   * // array (
   * //   1 => array (
   * //     'id' => 1,
   * //     'name' => 'Oliver',
   * //   ),
   * //   2 => array (
   * //     'id' => 2,
   * //     'name' => 'Harry',
   * //   ),
   * // )
   * ```
   * @param string $column (optional) Column Name. Default is "id".
   * @return array An object in which the column name is the key and the select result is the value.
   */
  public function result_keyvalue(string $column='id'): array {
    $rows = $this->result_array();
    if (empty($rows))
      return [];
    if (array_key_exists($column, $rows[0]) === false)
      throw new RuntimeException('result has no ' . $column . 'key');
    return array_combine(array_column($rows, $column), $rows);
  }
}