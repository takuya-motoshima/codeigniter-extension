<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Util\Logger;

class Model extends AppController {
  protected $model = 'UserModel';
  public function index() {
    try {
      $count = $this->UserModel->count_by_id(1);
      Logger::print($this->UserModel->last_query());
      Logger::print("Count of records with id 1:$count");

      $exists = $this->UserModel->exists_by_id(1);
      Logger::print($this->UserModel->last_query());
      Logger::print('Existence of record with ID 1:', $exists ? 1 : 0);

      $users = $this->UserModel
        ->select('id, name')
        ->get()
        ->result_array();
      $query = $this->UserModel->last_query();
      Logger::print($query);// SELECT `id`, `name` FROM `user`
    } catch (\Throwable $e) {
      Logger::print($e->getMessage());
    }
  }
}