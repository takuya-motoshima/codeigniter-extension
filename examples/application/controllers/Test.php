<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\Access;
use \X\Util\Logger;
use \X\Model\Model;
class Test extends AppController {

  protected $model = 'TestModel';

  public function index() {
    parent::view('test/index');
  }

  /**
   * @Access(allow_login=true, allow_logoff=true)
   */
  public function transactionTest() {
    try {
      $this->TestModel->testTransaction();
    } catch (\Throwable $e) {
      Logger::print($e->getMessage());
    }
  }

  /**
   * @Access(allow_login=true, allow_logoff=true)
   */
  public function dbConnectionTest() {
    try {
      $connected = Model::is_connect() ? 1 : 0;
      Logger::print('$connected=', $connected ? 1 : 0);
    } catch (\Throwable $e) {
      Logger::print($e->getMessage());
    }
  }
}