<?php
namespace X\Util;
use \X\Util\Loader;

/**
 * Cipher utility.
 */
final class Cipher {
  /**
   * Encode with SHA-256.
   * @param string $plaintext String to be hashed.
   * @param string|null $key (optional) Encoding key. If not specified, get it from "encryption_key" in "application/config/config.php".
   * @return string
   */
  public static function encode_sha256(string $plaintext, string $key=null): string {
    if (empty($key))
      $key = Loader::config('config', 'encryption_key');
    if (empty($key))
      throw new \RuntimeException('Cant find encryption_key in application/config/config.php file');
    return hash('sha256', $plaintext . $key);
  }

  /**
   * Generate IV.
   * @param string $algorithm (optional) Cryptographic Algorithm. Default is "AES-256-CTR".
   * @return string IV.
   */
  public static function generateInitialVector(string $algorithm='AES-256-CTR'): string {
    $len = openssl_cipher_iv_length($algorithm);
    return openssl_random_pseudo_bytes($len);
  }

  /**
   * Encryption.
   * ```php
   * use \X\Util\Cipher;
   *
   * $iv = Cipher::generateInitialVector();
   * $plaintext = 'Hello, World.';
   * $key = 'key';
   * $encrypted = Cipher::encrypt($plaintext, $key, $iv);// UHLY5PckT7Da02e42g==
   * $decrypted = Cipher::decrypt($encrypted, $key, $iv);// Hello, World.
   * ```
   * @param string $plaintext String to be encrypted.
   * @param string $key Encryption key.
   * @param string $iv IV.
   * @param string $algorithm (optional) Cryptographic Algorithm. Default is "AES-256-CTR".
   * @return string Encrypted string.
   */
  public static function encrypt(string $plaintext, string $key, string $iv, string $algorithm='AES-256-CTR'): string {
    $options = 0;
    return openssl_encrypt($plaintext, $algorithm, $key, $options, $iv);
  }

  /**
   * Decryption.
   * ```php
   * use \X\Util\Cipher;
   *
   * $iv = Cipher::generateInitialVector();
   * $plaintext = 'Hello, World.';
   * $key = 'key';
   * $encrypted = Cipher::encrypt($plaintext, $key, $iv);// UHLY5PckT7Da02e42g==
   * $decrypted = Cipher::decrypt($encrypted, $key, $iv);// Hello, World.
   * ```
   * @param string $encrypted Encrypted string.
   * @param string $key Decryption key.
   * @param string $iv IV.
   * @param string $algorithm (optional) Cryptographic Algorithm. Default is "AES-256-CTR".
   * @return string Decrypted string.
   */
  public static function decrypt(string $encrypted, string $key, string $iv, string $algorithm='AES-256-CTR'): string {
    $options = 0;
    return openssl_decrypt($encrypted, $algorithm, $key, $options, $iv);
  }

  /**
   * Generate a random key.
   * @param int $len (optional) Key length. Default is 32.
   * @return string Key.
   */
  public static function generateKey(int $len=32): string {
    if ($len < 1)
      throw new RuntimeException('Key length must be 1 or more');
    return base64_encode(random_bytes($len));
  }

  /**
   * Generate OpenSSL Key Pair
   * ```php
   * use \X\Util\Cipher;
   * 
   *  // Generate 4096bit long RSA key pair.
   *  Cipher::generateKeyPair($privateKey, $publicKey, [
   *    'digest_alg' => 'sha512',
   *    'private_key_bits' => 4096,
   *    'private_key_type' => OPENSSL_KEYTYPE_RSA
   *  ]);
   * 
   *  // Debug private key.
   *  // Output: -----BEGIN PRIVATE KEY-----
   *  //         MIIJQgIBADANBgkqhkiG9w0BAQEFAASCCSwwggkoAgEAAoICAQCpvdXUNEfrA4T+
   *  //         ...
   *  //         -----END PRIVATE KEY-----
   *  echo 'Private key:'. PHP_EOL . $privateKey;
   * 
   *  // Debug public key.
   *  // Output: -----BEGIN PUBLIC KEY-----
   *  //         MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAqb3V1DRH6wOE/oVhJWEo
   *  //         ...
   *  //         -----END PUBLIC KEY-----
   *  echo 'Public key:' . PHP_EOL. $publicKey;
   *  
   *  // OpenSSH encode the public key.
   *  // Output: ssh-rsa AAAAB3NzaC...
   *  $publicKey = Cipher::encodeOpenSshPublicKey($privateKey);
   * 
   *  // Debug OpenSSH-encoded public key.
   *  echo 'OpenSSH-encoded public key:' . PHP_EOL . $publicKey;
   * ```
   * @param string &$privateKey The generated private key is set.
   * @param string &$publicKey The generated public key is set.
   * @param string $options[digest_alg] Digest method or signature hash, usually one of openssl_get_md_methods(). The default value is "sha512".
   * @param string $options[x509_extensions] Selects which extensions should be used when creating an x509 certificate. The default value is none.
   * @param string $options[req_extensions] Selects which extensions should be used when creating a CSR. The default value is none.
   * @param int $options[private_key_bits] Specifies how many bits should be used to generate a private key. The default value is 4096.
   * @param int $options[private_key_type] Specifies the type of private key to create. This can be one of OPENSSL_KEYTYPE_DSA, OPENSSL_KEYTYPE_DH, OPENSSL_KEYTYPE_RSA or OPENSSL_KEYTYPE_EC. The default value is OPENSSL_KEYTYPE_RSA.
   * @param bool $options[encrypt_key] Should an exported key (with passphrase) be encrypted?
   * @param int $options[encrypt_key_cipher] One of cipher constants. The default value is none.
   * @param string $options[curve_name] One of openssl_get_curve_names(). The default value is none.
   * @param string $options[config] Path to your own alternative openssl.conf file. The default value is none.
   * @return void
   */
  public static function generateKeyPair(&$privateKey, &$publicKey, array $options=[]): void {
    $options = array_merge([
      'digest_alg' => 'sha512',
      'private_key_bits' => 4096,
      'private_key_type' => OPENSSL_KEYTYPE_RSA
    ], $options);
    $privateKeyResource = openssl_pkey_new($options);
    openssl_pkey_export($privateKeyResource, $privateKey);
    $publicKey = openssl_pkey_get_details($privateKeyResource)['key'];
  }

  /**
   * Encode OpenSSH public key.
   * @param string $privateKey Private Key.
   * @return string SSH-encoded Public key.
   */
  public static function encodeOpenSshPublicKey(string $privateKey): string {
    $privateKeyResource = openssl_pkey_get_private($privateKey);
    $keyInfo = openssl_pkey_get_details($privateKeyResource);
    $buffer  = pack('N', 7) . 'ssh-rsa' . self::encodeOpenSshBuffer($keyInfo['rsa']['e']) . self::encodeOpenSshBuffer($keyInfo['rsa']['n']);
    return 'ssh-rsa ' . base64_encode($buffer);
  }

  /**
   * OpenSSH encode the buffer.
   * @param string $buffer buffer.
   * @return string SSH encoded buffer.
   */
  private static function encodeOpenSshBuffer(string $buffer): string {
    $len = strlen($buffer);
    if (ord($buffer[0]) & 0x80) {
      $len++;
      $buffer = "\x00" . $buffer;
    }
    return pack('Na*', $len, $buffer);
  }

  /**
   * Generate a random string.
   * @param int $len (optional) Characters. Default is 64.
   * @param string $chars (optional) Characters to be used. Default is one-byte alphanumeric characters.
   * @return string Random string.
   */
  public static function randStr(int $len=64, string $chars='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'): string {
    if ($len < 1)
      throw new \RangeException('Length must be a positive integer');
    $res = '';
    for ($i=0; $i<$len; $i++)
      $res .= $chars[random_int(0, strlen($chars) - 1)];
    return $res;
  }

  /**
   * Generate a random token68 string.
   * @param int $len (optional) Characters. Default is 64.
   * @return string token68 string.
   */
  public static function randToken68(int $len=64): string {
    $equal = $len > 1 && random_int(0, 1) === 1 ? '=' : '';
    return self::randStr($len - strlen($equal), '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-._~+/') . $equal;
  }
}