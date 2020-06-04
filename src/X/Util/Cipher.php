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
   * @param  string $clearText
   * @return string
   */
  public static function encode_sha256(string $clearText, string $encryptionKey = null): string {
    if (empty($encryptionKey)) {
      $encryptionKey = Loader::config('config', 'encryption_key');
    }
    if (empty($encryptionKey)) {
      throw new \RuntimeException('Cant find encryption_key in application/config/config.php file');
    }
    return hash('sha256', $clearText . $encryptionKey);
  }

  /**
   * @param string $clearText
   * @param string $iv
   * @return string
   */
  public static function encrypt(string $clearText, string $iv = null, string $method = null): string {
    if (empty(Loader::config('config', 'openssl_key'))) {
      throw new \RuntimeException('Cant find openssl_key in application/config/config.php file');
    }
    $method = ($method ?? Loader::config('config', 'openssl_method')) ?? 'AES-256-CTR';
    $options = 0;
    return openssl_encrypt($clearText, $method, Loader::config('config', 'openssl_key'), $options, $iv);
  }

  /**
   * @param string $encryptedText
   * @param string $iv
   * @return string
   */
  public static function decrypt(string $encryptedText, string $iv = null, string $method = null): string {
    if (empty(Loader::config('config', 'openssl_key'))) {
      throw new \RuntimeException('Cant find openssl_key in application/config/config.php file');
    }
    $method = ($method ?? Loader::config('config', 'openssl_method')) ?? 'AES-256-CTR';
    $options = 0;
    return openssl_decrypt($encryptedText, $method, Loader::config('config', 'openssl_key'), $options, $iv);
  }

  /**
   * @deprecated deprecated since version 3.1.0
   */
  public static function encode(string $clearText): string {
    return self::encrypt($clearText);
  }

  /**
   * @deprecated deprecated since version 3.1.0
   */
  public static function decode(string $encryptedText): string {
    return self::decrypt($encryptedText);
  }
}