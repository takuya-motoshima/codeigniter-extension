<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\Access;
use \X\Util\Logger;
class Test extends AppController {

  protected $model = 'TestModel';

  /**
   * @Access(allow_login=true, allow_logoff=true)
   */
  public function transactionTest() {
    // $this->TestModel->testTransaction();
    Logger::debug('AppModel::is_connect()=', AppModel::is_connect() ? 1 : 0);
  }

  /**
   * @Access(allow_login=true, allow_logoff=true)
   */
  public function dbConnectionTest() {
    // $this->TestModel->testTransaction();
    Logger::debug('AppModel::is_connect()=', AppModel::is_connect() ? 1 : 0);
  }
}