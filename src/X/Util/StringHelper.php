<?php
/**
 * String helper class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Util;
final class StringHelper {

  /**
   * Trim
   *
   * @param  string $str
   * @param  string $addReplacement
   * @return string
   */
  public static function trim(string $str):string {
    return trim($str, " \t\n\r\0\x0B　");
  }

  /**
   * Omit if the string is too long
   *
   * @param  string $str
   * @param  int $length
   * @return string
   */
  public static function ellipsis(string $str, int $length = 100): string {
    if (strlen($str) <= $length) {
      return $str;
    }
    $dot = '…';
    $length -= strlen($dot);
    $beforeLength = floor($length/2);
    $afterLength = $length - $beforeLength;
    return substr($str, 0, $beforeLength) . '...' . substr($str, -$afterLength);
  }
}