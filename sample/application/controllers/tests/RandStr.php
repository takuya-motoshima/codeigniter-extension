<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Util\Cipher;
use \X\Util\Logger;

class RandStr extends AppController {
  /**
   * @example
   * sudo -u nginx CI_ENV=development php public/index.php tests/randStr;
   */
  public function index() {
    try {
      Logger::print(Cipher::rand_str());// YnqHuuG1VZJ1YXJC14RLmcVjg9uaa8jCyq8S8wd5uY7ox7PXEVzck2YTWGE7aftz
      Logger::print(Cipher::rand_str(10));// f1eXb3OLWq
      Logger::print(Cipher::rand_str(10, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-._~+/')); // 0e-k3qRu9z
      Logger::print(Cipher::rand_token68());// 1C63SpTuQfYlNs1IAvCclo~R2xgtrdNsNSa_U28G88mEFsrbz4yu3hn6_vIP7mS=
      Logger::print(Cipher::rand_token68(10));// OSVhnIAlJ=
    } catch (\Throwable $e) {
      Logger::print($e->getMessage());
    }
  }
}