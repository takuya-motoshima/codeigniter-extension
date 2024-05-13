<?php
namespace X\Util;
use MathieuViossat\Util\ArrayToTextTable;

/**
 * Array utility.
 */
final class ArrayHelper {
  /**
   * Searches for a value by key from an array.
   * ```php
   * use \X\Util\ArrayHelper;
   *
   * // Search from a simple array.
   * ArrayHelper::searchArrayByKey('France', [
   *   'France' => 'Paris',
   *   'UK' => 'London',
   *   'USA' => 'New York'
   * ]);// => Paris
   * 
   * // Search from nested array.
   * ArrayHelper::searchArrayByKey('USA', [
   *   'cities' => [
   *     'France' => 'Paris',
   *     'UK' => 'London',
   *     'USA' => 'New York'
   *   ]
   * ]);// => New York
   * ```
   * @param string $needle Key of the array to search.
   * @param array $arr Array.
   * @return mixed|null The value of the array found.
   */
  public static function searchArrayByKey(string $needle, array $arr) {
    foreach (new \RecursiveIteratorIterator(new \RecursiveArrayIterator($arr)) as $key => $value)
      if ($needle === $key)
        return $value;
    return null;
  }

  /**
   * Reset array keys (0,1,2...).
   * @param array $arr Array.
   * @return array Array.
   */
  public static function resetArrayKeys(array $arr): array {
    return array_values($arr);
  }

  /**
   * Randomly takes elements out of an array. The array passed as an argument is also modified.
   * @param array &$arr Array.
   * @return mixed Array elements.
   */
  public static function getRandomValue(array &$arr) {
    if (empty($arr))
      return null;
    $key = array_rand($arr, 1);
    $value = $arr[$key];
    unset($arr[$key]);
    return $value;
  }

  /**
   * Grouping of associative arrays by specified key.
   * ```php
   * use \X\Util\ArrayHelper;
   * 
   * $foods = [
   *   ['name' => 'Apple', 'category' => 'fruits'],
   *   ['name' => 'Strawberry', 'category' => 'fruits'],
   *   ['name' => 'Tomato', 'category' => 'vegetables'],
   *   ['name' => 'Carot', 'category' => 'vegetables'],
   * ];
   * ArrayHelper::grouping($foods, 'category');
   * // [
   * //   'fruits' => [
   * //     ['name' => 'Apple', 'category' => 'fruits'],
   * //     ['name' => 'Strawberry', 'category' => 'fruits']
   * //   ],
   * //   'vegetables' => [
   * //     ['name' => 'Tomato', 'category' => 'vegetables'],
   * //     ['name' => 'Carot', 'category' => 'vegetables']
   * //   ],
   * // ]
   * ```
   * @param array $arr Array.
   * @param string $groupBy Group key.
   * @return array Grouped arrays.
   */
  public static function grouping(array $arr, string $groupBy): array {
    return array_reduce($arr, function (array $groups, array $row) use ($groupBy) {
      $groups[$row[$groupBy]][] = $row;
      return $groups;
    }, []);
  }

  /**
   * Returns true if the subscript is an array starting from 0.
   * @param array $arr Array.
   * @return bool Returns true for vector arrays.
   */
  public static function isVector(array $arr): bool {
    return array_values($arr) === $arr;
  }

  /**
   * Returns an array as a tabular string.
   * ```php
   * use \X\Util\ArrayHelper;
   * 
   * $arr = [
   *   ['firstname' => 'John', 'lastname' => 'Mathew', 'email' => 'John.Mathew@xyz.com'],
   *   ['firstname' => 'Jim', 'lastname' => 'Parker', 'email' => 'Jim.Parker@xyz.com'],
   * ];
   * echo ArrayHelper::toTable($arr);
   * ┌───────────┬──────────┬─────────────────────┐
   * │ FIRSTNAME │ LASTNAME │        EMAIL        │
   * ├───────────┼──────────┼─────────────────────┤
   * │ John      │ Mathew   │ John.Mathew@xyz.com │
   * │ Jim       │ Parker   │ Jim.Parker@xyz.com  │
   * └───────────┴──────────┴─────────────────────┘
   * ```
   * @param array $arr Array.
   * @return string Returns a tabular string.
   */
  public static function toTable(array $arr): string {
    $renderer = new ArrayToTextTable(self::isVector($arr) ? $arr : [$arr]);
    return $renderer->getTable();
  }

  /**
    * Create an associative array, or an array of only the elements of any key from an associative array list.
    * ```php
    * use \X\Util\ArrayHelper;
    * 
    * $staffs = [
    *   ['name' => 'Derek Emmanuel', 'regno' => 'FE/30304', 'email' => 'derekemmanuel@gmail.com'],
    *   ['name' => 'Rubecca Michealson', 'regno' => 'FE/20003', 'email' => 'rmichealsongmail.com'],
    *   ['name' => 'Frank Castle', 'regno' => 'FE/10002', 'email' => 'fcastle86@gmail.com'],
    * ];
    * $staffs = ArrayHelper::filteringElements($staffs, 'name', 'email');
    * print_r($staffs);
    * // Array
    * // (
    * //     [0] => Array
    * //         (
    * //             [name] => Derek Emmanuel
    * //             [email] => derekemmanuel@gmail.com
    * //         )
    * //     [1] => Array
    * //         (
    * //             [name] => Rubecca Michealson
    * //             [email] => rmichealsongmail.com
    * //         )
    * //     [2] => Array
    * //         (
    * //             [name] => Frank Castle
    * //             [email] => fcastle86@gmail.com
    * //         )
    * // )
    * 
    * $staff = ['name' => 'Derek Emmanuel', 'regno' => 'FE/30304', 'email' => 'derekemmanuel@gmail.com'];
    * $staff = ArrayHelper::filteringElements($staff, 'name', 'email');
    * print_r($staff);
    * // Array
    * // (
    * //     [name] => Derek Emmanuel
    * //     [email] => derekemmanuel@gmail.com
    * // )
    * ```
    * @param array $arr Array.
    * @param mixed ...$includeKey Key of the array element to be retrieved.
    * @return array Array.
    */
  public static function filteringElements(array $arr, ...$includeKey):array {
    if (empty($arr))
      return $arr;
    if (isset($arr[0]))
      return array_map(function($value) use ($includeKey) {
        return array_filter($value, function ($key) use ($includeKey) {
          return in_array($key, $includeKey);
        }, ARRAY_FILTER_USE_KEY);
      }, $arr);
    else
      return array_filter($arr, function ($key) use ($includeKey) {
        return in_array($key, $includeKey);
      }, ARRAY_FILTER_USE_KEY);
  }
}