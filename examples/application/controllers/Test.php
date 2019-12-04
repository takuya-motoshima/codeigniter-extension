<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use \X\Annotation\Access;
use \X\Util\Logger;
use \X\Util\Cipher;
class Test extends AppController {

  public function index() {
    ini_set('display_errors', 0);

    try {
      $str = 'hello';
      $encrypted = Cipher::encode_sha256($str);
      Logger::s("encode_sha256 $str -> $encrypted");
    } catch (\Throwable $e) {
      Logger::s('encode_sha256 error:', $e->getMessage());
    }

    try {
      $str = 'hello';
      $encrypted = Cipher::encode($str);
      Logger::s("encode $str -> $encrypted");
      $decrypted = Cipher::decode($encrypted);
      Logger::s("decode $encrypted -> $decrypted");
    } catch (\Throwable $e) {
      Logger::s('encode error:', $e->getMessage());
    }

    try {
      $str = 'hello';
      $encrypted = Cipher::encrypt($str);
      Logger::s("encrypt $str -> $encrypted");
      $decrypted = Cipher::decrypt($encrypted);
      Logger::s("decrypt $encrypted -> $decrypted");
    } catch (\Throwable $e) {
      Logger::s('encrypt error:', $e->getMessage());
    }
  }
}