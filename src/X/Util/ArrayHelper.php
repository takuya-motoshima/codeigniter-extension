<?php
/**
 * Array helper class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2018 Takuya Motoshima
 */
namespace X\Util;

final class ArrayHelper {

  /**
   * Searches and returns the value of the specified key from the array
   *
   * @example
   * use \X\Util\ArrayHelper;
   *
   * // Search from a simple array
   * $array = [
   *   'France' => 'Paris',
   *   'India' => 'Mumbai',
   *   'UK' => 'London',
   *   'USA' => 'New York'
   * ];
   * 
   * ArrayHelper::searchArrayByKey('France', $array);
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
   * 
   * ArrayHelper::searchArrayByKey('USA', $nested);
   * // New York
   * @param  string $needle
   * @param  array $haystack
   * @return mixed
   */
  public static function searchArrayByKey(string $needle, array $array) {
    foreach (new \RecursiveIteratorIterator(new \RecursiveArrayIterator($array)) as $key => $value) {
      if ($needle === $key) {
        return $value;
      }
    }
    return null;
  }

  /**
   * Returns an array with the keys of the array initialized to 0,1,2 ...
   *
   * @param  array $array
   * @return array
   */
  public static function resetArrayKeys(array $array):array {
    return array_values($array);
  }

  /**
   * Returns a unique value from an array
   *
   * @param  array $array
   * @return mixed
   */
  public static function getRandomValue(array &$array) {
    if (empty($array)) {
      return null;
    }
    $key = array_rand($array, 1);
    $value = $array[$key];
    unset($array[$key]);
    return $value;
  }
}