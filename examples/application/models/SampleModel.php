<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Util\Logger;
class SampleModel extends AppModel {

  const TABLE = 'sample';

  public function getById(int $id): ?array {
    return parent::get_by_id($id);
  }

  public function add(array $set): int {
    try {
      parent::trans_begin();
      $id = parent::insert(self::TABLE, $set);
      if (parent::trans_status() === false) {
        throw RuntimeException(parent::error()['message']);
      }
      parent::trans_commit();
      return $id;
    } catch (\Throwable $e) {
      Logger::error($e);
      parent::trans_rollback();
      throw $e;
    }
  }

  public function updateById(int $id, array $set) {
    try {
      parent::trans_begin();
      parent
        ::where('id', $id)
        ::update(self::TABLE, $set);
      if (parent::trans_status() === false) {
        throw RuntimeException(parent::error()['message']);
      }
      parent::trans_commit();
    } catch (\Throwable $e) {
      Logger::error($e);
      parent::trans_rollback();
      throw $e;
    }
  }

  public function deleteById(int $id) {
    try {
      parent::trans_begin();
      parent
        ::where('id', $id)
        ::delete(self::TABLE);
      if (parent::trans_status() === false) {
        throw RuntimeException(parent::error()['message']);
      }
      parent::trans_commit();
    } catch (\Throwable $e) {
      Logger::error($e);
      parent::trans_rollback();
      throw $e;
    }
  }
}