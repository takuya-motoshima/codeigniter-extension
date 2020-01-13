<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Util\Logger;
class TestModel extends \AppModel {

  const TABLE = 'test';

  public function getById(int $id): ?array {
    return parent::get_by_id($id);
  }

  public function add(array $set) {

    try {

      parent::trans_begin();
      parent::insert(self::TABLE, $set);
      if (parent::trans_status() === false) {
        throw RuntimeException(parent::error()['message']);
      }
      parent::trans_commit();
    } catch (\Throwable $e) {
      Logger::error($e);
      parent::trans_rollback();
    }
  }

  public function testTransaction() {

    try {

      parent::trans_begin();
      Logger::debug('Register a record with ID 1 and name Elsabith');
      parent::insert(self::TABLE, [ 'id' => 1, 'name' => 'Elsabith' ]);
      Logger::debug('Register a record with ID 1 and name Florence');
      try {
        parent::insert(self::TABLE, [ 'id' => 1, 'name' => 'Florence' ]);
      } catch (\Throwable $e) {
        Logger::error('Second registration failed');
      }
      
      Logger::debug('parent::trans_status()=', parent::trans_status() ? 1 : 0);
      if (parent::trans_status() === false) {
        throw RuntimeException(parent::error()['message']);
      }
      Logger::debug('commit');
      parent::trans_commit();
    } catch (\Throwable $e) {
      Logger::debug('rollback');
      Logger::error($e);
      parent::trans_rollback();
    }
  }
}