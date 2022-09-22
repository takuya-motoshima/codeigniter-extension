<?php
namespace X\Database;
trait Result {
  /**
   * Query result. "array of the form KeyValue" version.
   * <code>
   * <?php
   * $rows = parent
   *   select('id,name')::
   *   from('user')::
   *   get()->
   *   result_keyvalue('id');
   * var_export($rows);
   * // array (
   * //   1 => array (
   * //     'id' => 1,
   * //     'name' => 'Oliver',
   * //     'department' => 'Administration',
   * //   ),
   * //   2 => array (
   * //     'id' => 2,
   * //     'name' => 'Harry',
   * //     'department' => 'Marketing',
   * //   ),
   * // )
   * </code>
   *
   * @throws RuntimeException
   * @param  string $key = 'id'
   * @return  array
   */
  public function result_keyvalue(string $key = 'id'):array {
    $rows = $this->result_array();
    if (empty($rows))
      return [];
    if (array_key_exists($key, $rows[0]) === false)
      throw new RuntimeException('result has no ' . $key . 'key');
    return array_combine(array_column($rows, $key), $rows);
  }
}