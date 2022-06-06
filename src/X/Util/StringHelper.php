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
   * Returns characters with left and right whitespace trimmed.
   *
   * @param  string $str
   * @return string
   */
  public static function trim(?string $str):string {
    return trim($str, " \t\n\r\0\x0B　");
  }

  /**
   * Returns true if the whitespace trimmed character is empty.
   *
   * @param  string $str
   * @return bool
   */
  public static function empty(?string $str):bool {
    return empty(self::trim($str));
  }

  /**
   * Omit if the string is too long.
   *
   * @param  string     $str
   * @param  int        $length
   * @return string
   */
  public static function ellipsis(string $str, int $length = 100): string {
    if (mb_strlen($str) <= $length)
      return $str;
    $dot = '…';
    $length -= mb_strlen($dot);
    $beforeLength = floor($length/2);
    $afterLength = $length - $beforeLength;
    return mb_substr($str, 0, $beforeLength) . '...' . mb_substr($str, -$afterLength);
  }
}