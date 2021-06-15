<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\Access;
use \X\Util\Logger;

class ModelTest extends AppController {

  protected $model = 'UserModel';

  /**
   * @Access(allow_login=true, allow_logoff=true)
   */
  public function index() {
    try {
      $count = $this->UserModel->count_by_id(1);
      Logger::print($this->UserModel->last_query());
      Logger::print("Count of records with id 1:$count");

      $exists = $this->UserModel->exists_by_id(1);
      Logger::print($this->UserModel->last_query());
      Logger::print('Existence of record with ID 1:', $exists ? 1 : 0);
    } catch (\Throwable $e) {
      Logger::print($e->getMessage());
    }
  }
}