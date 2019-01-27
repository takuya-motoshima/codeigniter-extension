<?php

/**
 * Iterator class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2019 Takuya Motoshima
 */
namespace X\Util;
final class Iterator
{
 
  /**
   * 重複あり順列
   * 1.1回取り出したものを再度取り出せる
   * 2.取り出した順番は、意味を成す(組を構成する要素が同じでも、順番が違えば別のものとして扱う)
   * 
   * i.e:
   *    > \X\Util\Iterator::duplicatePermutation('abc', 2, '', $result);
   *    > var_export($result);
   *    array (
   *      0 => 'aa',
   *      1 => 'ab',
   *      2 => 'ac',
   *      3 => 'ba',
   *      4 => 'bb',
   *      5 => 'bc',
   *      6 => 'ca',
   *      7 => 'cb',
   *      8 => 'cc',
   *    )
   * @param      string $data
   * @param      int    $r
   * @param      string $progress
   * @param      ?array $result
   */
  public static function duplicatePermutation(string $data, int $r, string $progress='', ?array &$result = [])
  {
    if ($r === 0) {
      $result[] = $progress;
      return;
    }
    for ($i=0,$len=strlen($data); $i<$len; $i++) {
      self::duplicatePermutation($data, $r - 1, $progress . $data[$i], $result);
    }
  }
  
  /**
   * 重複なし順列
   * 
   * 1.1回取り出したものを再度取り出せない
   * 2.取り出した順番は、意味を成す(組を構成する要素が同じでも、順番が違えば別のものとして扱う)
   * 
   * i.e:
   *    > \X\Util\Iterator::unDuplicatePermutation('abc', 2, '', $result);
   *    > var_export($result);
   *    array (
   *      0 => 'ab',
   *      1 => 'ac',
   *      2 => 'ba',
   *      3 => 'bc',
   *      4 => 'ca',
   *      5 => 'cb',
   *    )
   * @param      string  $data
   * @param      integer $r
   * @param      string  $progress
   * @param      ?array  $result
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
   * 重複あり組み合わせ
   * 
   * 1.1回取り出したものを再度取り出せる
   * 2.取り出した順番は、意味を成さない(組を構成する要素が同じならば、順番が違っても同じものとして扱う)
   * 
   * i.e:
   *    > \X\Util\Iterator::duplicateCombinations('abc', 2, 0, '', $result);
   *    > var_export($result);
   *    array (
   *      0 => 'aa',
   *      1 => 'ab',
   *      2 => 'ac',
   *      3 => 'bb',
   *      4 => 'bc',
   *      5 => 'cc',
   *    )
   * @param      string  $data
   * @param      integer $r
   * @param      integer $start
   * @param      string  $progress
   * @param      ?array  $result
   */
  public static function duplicateCombinations(string $data, int $r, int $start = 0, string $progress='', ?array &$result = []) {
    if ($r === 0) {
      $result[] = $progress;
      return;
    }
    for ($i=$start; $i<strlen($data); $i++) {
      self::duplicateCombinations($data, $r-1, $i, $progress . $data[$i], $result);
    }
  }

  /**
   * 重複なし組み合わせ
   * 
   * 1.1回取り出したものを再度取り出せない
   * 2.取り出した順番は、意味を成さない(組を構成する要素が同じならば、順番が違っても同じものとして扱う)
   * 
   * i.e:
   *    > \X\Util\Iterator::unDuplicateCombinations('abc', 2, 0, '', $result);
   *    > var_export($result);
   *    array (
   *      0 => 'ab',
   *      1 => 'ac',
   *      2 => 'bc',
   *    )
   * @param      string  $data
   * @param      integer $r
   * @param      integer $start
   * @param      string  $progress
   * @param      ?array  $result
   */
  public static function unDuplicateCombinations(string $data, int $r, int $start = 0, string $progress='', ?array &$result = []) {
    if ($r === 0) {
      $result[] = $progress;
      return;
    }
    for ($i=$start; $i<strlen($data); $i++) {
      self::unDuplicateCombinations($data, $r-1, $i + 1, $progress . $data[$i], $result);
    }
  }
}