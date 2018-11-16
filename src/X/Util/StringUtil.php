<?php
/**
 * String util class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Util;
final class StringUtil
{

  /**
   * Trim
   *
   * @param  string $str
   * @param  string $addReplacement
   * @return string
   */
  public static function trim(string $str):string
  {
    return trim($str, " \t\n\r\0\x0B　");
  }
}