<?php
namespace X\Util;

/**
 * String Utility.
 */
final class StringHelper {
  /**
   * Trim front and rear spaces.
   * @param string|null $str String.
   * @return string String.
   */
  public static function trim(?string $str): string {
    return trim($str, " \t\n\r\0\x0B　");
  }

  /**
   * Trims the string to determine if it is empty.
   * @param string|null $str String.
   * @return string String.
   */
  public static function empty(?string $str): bool {
    return empty(self::trim($str));
  }

  /**
   * Omit strings that are too long. Characters longer than the specified length will be replaced by "...".
   * @param string $str String.
   * @param int $length (optional) Length of string. Default is 100.
   * @return string String.
   */
  public static function ellipsis(string $str, int $length=100): string {
    if (mb_strlen($str) <= $length)
      return $str;
    $dot = '…';
    $length -= mb_strlen($dot);
    $beforeLength = floor($length/2);
    $afterLength = $length - $beforeLength;
    return mb_substr($str, 0, $beforeLength) . '...' . mb_substr($str, -$afterLength);
  }
}