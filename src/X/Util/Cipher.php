<?php
/**
 * Cipher class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Util;
use \X\Util\Loader;

final class Cipher {

  /**
   * 
   * Encode SHA-256
   *
   * @param  string $plaintext
   * @param  string $key
   * @return string
   */
  public static function encode_sha256(string $plaintext, string $key = null): string {
    if (empty($key)) $key = Loader::config('config', 'encryption_key');
    if (empty($key)) throw new \RuntimeException('Cant find encryption_key in application/config/config.php file');
    return hash('sha256', $plaintext . $key);
  }

  /**
   * Generate initial vector.
   * 
   * @param string $method
   * @return string
   */
  public static function generateInitialVector(string $method = 'AES-256-CTR'): string {
    $length = openssl_cipher_iv_length($method);
    return openssl_random_pseudo_bytes($length);
  }

  /**
   * Encrypt.
   *
   * @example
   * use \X\Util\Cipher;
   *
   * // Get the initialization vector. This should be changed every time to make it difficult to predict.
   * $iv = Cipher::generateInitialVector();
   *
   * // Plaintext.
   * $plaintext = 'Hello, World.';
   *
   * // Encrypted key.
   * $key = 'key';
   *
   * // Encrypt.
   * $encrypted = Cipher::encrypt($plaintext, $key, $iv);// UHLY5PckT7Da02e42g==
   *
   * // Decrypt.
   * $decrypted = Cipher::decrypt($encrypted, $key, $iv);// Hello, World.
   * 
   * @param  string $plaintext
   * @param  string $key
   * @param  string $iv
   * @param  string $method
   * @return string
   */
  public static function encrypt(string $plaintext, string $key, string $iv, string $method = 'AES-256-CTR'): string {
    $options = 0;
    return openssl_encrypt($plaintext, $method, $key, $options, $iv);
  }

  /**
   * Decrypt.
   *
   * @example
   * use \X\Util\Cipher;
   *
   * // Get the initialization vector. This should be changed every time to make it difficult to predict.
   * $iv = Cipher::generateInitialVector();
   *
   * // Plaintext.
   * $plaintext = 'Hello, World.';
   *
   * // Encrypted key.
   * $key = 'key';
   *
   * // Encrypt.
   * $encrypted = Cipher::encrypt($plaintext, $key, $iv);// UHLY5PckT7Da02e42g==
   *
   * // Decrypt.
   * $decrypted = Cipher::decrypt($encrypted, $key, $iv);// Hello, World.
   * 
   * @param  string $encrypted
   * @param  string $key
   * @param  string $iv
   * @param  string $method
   * @return string
   */
  public static function decrypt(string $encrypted, string $key, string $iv, string $method = 'AES-256-CTR'): string {
    $options = 0;
    return openssl_decrypt($encrypted, $method, $key, $options, $iv);
  }

  /**
   * Generate a random key.
   * 
   * @param  int $length
   * @return string
   */
  public static function generateKey(int $length = 32): string {
    if ($length < 1) throw new RuntimeException('Key length must be 1 or more.');
    return base64_encode(random_bytes($length));
  }

  /**
   * @deprecated deprecated since version 3.1.0
   */
  public static function encode(string $plaintext): string {
    return self::encrypt($plaintext);
  }

  /**
   * @deprecated deprecated since version 3.1.0
   */
  public static function decode(string $encrypted): string {
    return self::decrypt($encrypted);
  }
}