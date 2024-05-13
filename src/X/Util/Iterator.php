<?php
namespace X\Util;

/**
 * Iterator utility.
 */
final class Iterator {
  /**
   * Permutations with duplicates.
   * - An item taken out once can be taken out again.
   * - The order in which the items are retrieved makes sense (even if the elements of a pair are the same, they are treated as different if they are in a different order).
   * ```php
   * \X\Util\Iterator::duplicatePermutation('abc', 2, '', $return);
   * var_export($return);
   * // array (
   * //   0 => 'aa',
   * //   1 => 'ab',
   * //   2 => 'ac',
   * //   3 => 'ba',
   * //   4 => 'bb',
   * //   5 => 'bc',
   * //   6 => 'ca',
   * //   7 => 'cb',
   * //   8 => 'cc'
   * // )
   * ```
   * @param string $str Input string.
   * @param int $r String length of the result of the combination.
   * @param string $progress String in progress.
   * @param array|null &$return Combination Results.
   * @return void
   */
  public static function duplicatePermutation(string $str, int $r, string $progress='', ?array &$return=[]): void {
    if ($r === 0) {
      $return[] = $progress;
      return;
    }
    for ($i=0,$len=strlen($str); $i<$len; $i++)
      self::duplicatePermutation($str, $r - 1, $progress . $str[$i], $return);
  }
  
  /**
   * Non-duplicate permutation.
   * - Once retrieved cannot be retrieved again.
   * - The order in which the items are taken out makes sense (even if the elements of a pair are the same, they are treated as different if they are taken out in a different order).
   * ```php
   * \X\Util\Iterator::unDuplicatePermutation('abc', 2, '', $return);
   * var_export($return);
   * // array (
   * //   0 => 'ab',
   * //   1 => 'ac',
   * //   2 => 'ba',
   * //   3 => 'bc',
   * //   4 => 'ca',
   * //   5 => 'cb',
   * // )
   * ```
   * @param string $str Input string.
   * @param int $r String length of the result of the combination.
   * @param string $progress String in progress.
   * @param array|null &$return Combination Results.
   * @return void
   */
  public static function unDuplicatePermutation(string $str, int $r, string $progress='', ?array &$return=[]): void {
    if ($r === 0) {
      $return[] = $progress;
      return;
    }
    for ($i=0; $i<strlen($str); $i++) {
      $excludedIndicesData = array_filter(str_split($str), function($var) use ($str, $i) {
        return $var != $str[$i];
      });
      $excludedIndicesData = implode('', $excludedIndicesData);
      self::unDuplicatePermutation($excludedIndicesData, $r-1, $progress . $str[$i], $return);
    }
  }

  /**
   * Duplicate combinations with duplicates
   * - What was taken out once can be taken out again
   * - The order in which the items are retrieved is meaningless (if the elements that make up the pair are the same, they are treated as the same even if they are in a different order)
   * ```php
   * \X\Util\Iterator::duplicateCombinations('abc', 2, 0, '', $return);
   * var_export($return);
   * // array (
   * //   0 => 'aa',
   * //   1 => 'ab',
   * //   2 => 'ac',
   * //   3 => 'bb',
   * //   4 => 'bc',
   * //   5 => 'cc',
   * // )
   * ```
   * @param string $str Input string.
   * @param int $r String length of the result of the combination.
   * @param int $start The position from which the input string is taken.
   * @param string $progress String in progress.
   * @param array|null &$return Combination Results.
   * @return void
   */
  public static function duplicateCombinations(string $str, int $r, int $start=0, string $progress='', ?array &$return=[]) {
    if ($r === 0) {
      $return[] = $progress;
      return;
    }
    for ($i=$start; $i<strlen($str); $i++)
      self::duplicateCombinations($str, $r-1, $i, $progress . $str[$i], $return);
  }

  /**
   * No duplicate combination.
   * - Once something is taken out once, it cannot be taken out again.
   * - The order in which the items are taken out is meaningless (if the elements of a pair are the same, they are treated as the same even if they are taken out in a different order).
   * ```php
   * \X\Util\Iterator::unDuplicateCombinations('abc', 2, 0, '', $return);
   * var_export($return);
   * // array (
   * //   0 => 'ab',
   * //   1 => 'ac',
   * //   2 => 'bc',
   * // )
   * @param string $str Input string.
   * @param int $r String length of the result of the combination.
   * @param int $start The position from which the input string is taken.
   * @param string $progress String in progress.
   * @param array|null &$return Combination Results.
   * @return void
   * ```
   */
  public static function unDuplicateCombinations(string $str, int $r, int $start=0, string $progress='', ?array &$return=[]): void {
    if ($r === 0) {
      $return[] = $progress;
      return;
    }
    for ($i=$start; $i<strlen($str); $i++)
      self::unDuplicateCombinations($str, $r-1, $i + 1, $progress . $str[$i], $return);
  }
}