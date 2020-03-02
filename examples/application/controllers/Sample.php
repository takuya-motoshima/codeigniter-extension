<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\Access;
use \X\Util\Logger;
use \X\Model\Model;
class Sample extends AppController {

  protected $model = 'SampleModel';

  public function index() {
    parent::view('sample/index');
  }

  /**
   * @Access(allow_login=true, allow_logoff=true)
   */
  public function something() {
    try {

      $name = 'john';
      $this->SampleModel->getUsersByNames($name);
    } catch (\Throwable $e) {
      Logger::print($e->getMessage());
    }
  }


  /**
   * @Access(allow_login=true, allow_logoff=true)
   */
  public function transactionTest() {
    try {
      $this->SampleModel->transactionTest();
    } catch (\Throwable $e) {
      Logger::print($e->getMessage());
    }
  }

  /**
   * @Access(allow_login=true, allow_logoff=true)
   */
  public function dbConnectionTest() {
    try {
      Logger::print('DB connection: ', Model::is_connect() ? 1 : 0);
    } catch (\Throwable $e) {
      Logger::print($e->getMessage());
    }
  }
}