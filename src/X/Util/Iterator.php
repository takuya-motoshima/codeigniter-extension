<?php
namespace X\Util;

final class Iterator {
  /**
   * permutations with duplicates
   * - an item taken out once can be taken out again
   * - the order in which the items are retrieved makes sense (even if the elements of a pair are the same, they are treated as different if they are in a different order)
   * <code>
   * <?php
   * \X\Util\Iterator::duplicatePermutation('abc', 2, '', $result);
   * var_export($result);
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
   * </code>
   */
  public static function duplicatePermutation(string $data, int $r, string $progress='', ?array &$result = []) {
    if ($r === 0) {
      $result[] = $progress;
      return;
    }
    for ($i=0,$len=strlen($data); $i<$len; $i++)
      self::duplicatePermutation($data, $r - 1, $progress . $data[$i], $result);
  }
  
  /**
   * Non-duplicate permutation
   * - once retrieved cannot be retrieved again
   * - the order in which the items are taken out makes sense (even if the elements of a pair are the same, they are treated as different if they are taken out in a different order)
   * <code>
   * <?php
   * \X\Util\Iterator::unDuplicatePermutation('abc', 2, '', $result);
   * var_export($result);
   * // array (
   * //   0 => 'ab',
   * //   1 => 'ac',
   * //   2 => 'ba',
   * //   3 => 'bc',
   * //   4 => 'ca',
   * //   5 => 'cb',
   * // )
   * </code>
   */
  public static function unDuplicatePermutation(string $data, int $r, string $progress='', ?array &$result = []) {
    if ($r === 0) {
      $result[] = $progress;
      return;
    }
    for ($i=0; $i<strlen($data); $i++) {
      $excludedIndicesData = array_filter(str_split($data), function($var) use ($data, $i) {
        return $var != $data[$i];
      });
      $excludedIndicesData = implode('', $excludedIndicesData);
      self::unDuplicatePermutation($excludedIndicesData, $r-1, $progress . $data[$i], $result);
    }
  }

  /**
   * Duplicate combinations with duplicates
   * - What was taken out once can be taken out again
   * - The order in which the items are retrieved is meaningless (if the elements that make up the pair are the same, they are treated as the same even if they are in a different order)
   * <code>
   * <?php
   * \X\Util\Iterator::duplicateCombinations('abc', 2, 0, '', $result);
   * var_export($result);
   * // array (
   * //   0 => 'aa',
   * //   1 => 'ab',
   * //   2 => 'ac',
   * //   3 => 'bb',
   * //   4 => 'bc',
   * //   5 => 'cc',
   * // )
   * </code>
   */
  public static function duplicateCombinations(string $data, int $r, int $start = 0, string $progress='', ?array &$result = []) {
    if ($r === 0) {
      $result[] = $progress;
      return;
    }
    for ($i=$start; $i<strlen($data); $i++)
      self::duplicateCombinations($data, $r-1, $i, $progress . $data[$i], $result);
  }

  /**
   * No duplicate combination
   * - Once something is taken out once, it cannot be taken out again
   * - The order in which the items are taken out is meaningless (if the elements of a pair are the same, they are treated as the same even if they are taken out in a different order)
   * <code>
   * <?php
   * \X\Util\Iterator::unDuplicateCombinations('abc', 2, 0, '', $result);
   * var_export($result);
   * // array (
   * //   0 => 'ab',
   * //   1 => 'ac',
   * //   2 => 'bc',
   * // )
   */
  public static function unDuplicateCombinations(string $data, int $r, int $start = 0, string $progress='', ?array &$result = []) {
    if ($r === 0) {
      $result[] = $progress;
      return;
    }
    for ($i=$start; $i<strlen($data); $i++)
      self::unDuplicateCombinations($data, $r-1, $i + 1, $progress . $data[$i], $result);
  }
}