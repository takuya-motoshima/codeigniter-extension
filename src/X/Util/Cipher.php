<?php
use \X\Util\Loader;

/**
 * Cipher class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Util;
final class Cipher {

  /**
   * 
   * Encode SHA-256
   *
   * @param  string $plain_text
   * @return string
   */
  public static function encode_sha256(string $plain_text): string {
    return hash('sha256', $plain_text . Loader::config('config', 'encryption_key'));
  }

  /**
   * 
   * Encode
   *
   * @param  string $plain_text
   * @return string
   */
  public static function encode(string $plain_text): string {
    $ci =& get_instance();
    return $ci->encrypt->encode($plain_text, Loader::config('config', 'encryption_key'));
  }

  /**
   * 
   * Decode
   *
   * @param  string $encrypted_text
   * @return string
   */
  public static function decode(string $encrypted_text): string {
    $ci =& get_instance();
    return $ci->encrypt->decode($encrypted_text, Loader::config('config', 'encryption_key'));
  }
}