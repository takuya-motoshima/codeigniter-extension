<?php
namespace X\Util;
use MathieuViossat\Util\ArrayToTextTable;

final class ArrayHelper {
  /**
   * Searches and returns the value of the specified key from the array
   * <code>
   * <?php
   * use \X\Util\ArrayHelper;
   *
   * // Search from a simple array
   * $arr = [
   *   'France' => 'Paris',
   *   'India' => 'Mumbai',
   *   'UK' => 'London',
   *   'USA' => 'New York'
   * ];
   * 
   * ArrayHelper::searchArrayByKey('France', $arr);
   * // Paris
   * 
   * // Search from nested array
   * $nested = [
   *   'cities' => [
   *     'France' => 'Paris',
   *     'India' => 'Mumbai',
   *     'UK' => 'London',
   *     'USA' => 'New York'
   *   ]
   * ];
   * </code>
   * 
   * ArrayHelper::searchArrayByKey('USA', $nested);
   * // New York
   * @param  string $needle
   * @param  array $haystack
   * @return mixed
   */
  public static function searchArrayByKey(string $needle, array $arr) {
    foreach (new \RecursiveIteratorIterator(new \RecursiveArrayIterator($arr)) as $key => $value) {
      if ($needle === $key)
        return $value;
    }
    return null;
  }

  /**
   * Returns an array with the keys of the array initialized to 0,1,2 ...
   *
   * @param  array $arr
   * @return array
   */
  public static function resetArrayKeys(array $arr):array {
    return array_values($arr);
  }

  /**
   * Returns a unique value from an array
   *
   * @param  array $arr
   * @return mixed
   */
  public static function getRandomValue(array &$arr) {
    if (empty($arr)) return null;
    $key = array_rand($arr, 1);
    $value = $arr[$key];
    unset($arr[$key]);
    return $value;
  }

  /**
   * Group associative arrays by key..
   * 
   * ```php
   * use \X\Util\ArrayHelper;
   *
   * $foods = [
   *   ['name' => 'Apple',       'category' => 'fruits'],
   *   ['name' => 'Strawberry',  'category' => 'fruits'],
   *   ['name' => 'Tomato',      'category' => 'vegetables'],
   *   ['name' => 'Carot',       'category' => 'vegetables'],
   *   ['name' => 'water',       'category' => 'drink'],
   *   ['name' => 'beer',        'category' => 'drink'],
   * ];
   * 
   * ArrayHelper::grouping($foods, 'category');
   * // [
   * //   'fruits' => [
   * //     ['name' => 'Apple',       'category' => 'fruits'],
   * //     ['name' => 'Strawberry',  'category' => 'fruits']
   * //   ],
   * //   'vegetables' => [
   * //     ['name' => 'Tomato',      'category' => 'vegetables'],
   * //     ['name' => 'Carot',       'category' => 'vegetables']
   * //   ],
   * //   'drink' => [
   * //     ['name' => 'water',       'category' => 'drink'],
   * //     ['name' => 'beer',        'category' => 'drink']
   * //   ]
   * // ]
   * ```
   *
   * @param  array  $arr      Arrays you want to group.
   * @param  string $groupkey Group key.
   * @return array            Grouped arrays.
   */
  public static function grouping(array $arr, string $groupkey): array {
    return array_reduce($arr, function (array $groups, array $row) use ($groupkey) {
      $groups[$row[$groupkey]][] = $row;
      return $groups;
    }, []);
  }


  /**
   * Returns true if the subscript is an array starting from 0.
   * 
   * @param  array   $arr Array
   * @return boolean      Returns true for vector arrays
   */
  public static function isVector(array $arr): bool {
    return array_values($arr) === $arr;
  }

  /**
   * Returns an array as a tabular string.
   * 
   * ```php
   * use \X\Util\ArrayHelper;
   * 
   * $arr = [
   *   [
   *     'firstname' => 'John',
   *     'lastname' => 'Mathew',
   *     'email' => 'John.Mathew@xyz.com'
   *   ],
   *   [
   *     'firstname' => 'Jim',
   *     'lastname' => 'Parker',
   *     'email' => 'Jim.Parker@xyz.com'
   *   ]
   * ];
   * echo ArrayHelper::toTable($arr);
   * ┌───────────┬──────────┬─────────────────────┐
   * │ FIRSTNAME │ LASTNAME │        EMAIL        │
   * ├───────────┼──────────┼─────────────────────┤
   * │ John      │ Mathew   │ John.Mathew@xyz.com │
   * │ Jim       │ Parker   │ Jim.Parker@xyz.com  │
   * └───────────┴──────────┴─────────────────────┘
   * ```
   * 
   * @param  array  $arr Array
   * @return string Returns a tabular string.
   */
  public static function toTable(array $arr): string {
    $renderer = new ArrayToTextTable(self::isVector($arr) ? $arr : [$arr]);
    return $renderer->getTable();
  }
}