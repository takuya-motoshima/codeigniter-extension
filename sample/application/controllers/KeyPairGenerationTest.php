<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\Access;
use \X\Util\Cipher;
use \X\Util\Logger;

class KeyPairGenerationTest extends AppController {

  /**
   * @Access(allow_login=true, allow_logoff=true)
   */
  public function index() {
    try {
      // Generate 4096bit long RSA key pair.
      Cipher::generateKeyPair($privKey, $pubKey, [
        'digest_alg' => 'sha512',
        'private_key_bits' => 4096,
        'private_key_type' => OPENSSL_KEYTYPE_RSA
      ]);

      // Debug private key.
      Logger::print('Private key:'. PHP_EOL . $privKey);

      // Debug public key.
      Logger::print('Public key:' . PHP_EOL. $pubKey);

      // OpenSSH encode the public key.
      $pubKey = Cipher::encodeOpenSshPublicKey($privKey);

      // Debug OpenSSH-encoded public key.
      Logger::print('OpenSSH-encoded public key:' . PHP_EOL . $pubKey);
    } catch (\Throwable $e) {
      Logger::print($e->getMessage());
    }
  }
}