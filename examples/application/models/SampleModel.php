<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Util\Logger;
class SampleModel extends AppModel {

  const TABLE = 'sample';

  public function getById(int $id): ?array {
    return parent::get_by_id($id);
  }

  public function testSubQuery() {
    $subQuery = parent
      ::select('name, COUNT(*) count')
      ::from(self::TABLE)
      ::group_by('name')
      ::get_compiled_select();

    Logger::debug('$subQuery=', $subQuery);

    $rows =parent
      ::select('counter.name, counter.count')
      ::from("($subQuery) counter")
      ::get()
      ->result_array();

    Logger::debug('$rows=', $rows);
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

  public function getUsersByNames(string $name = 'john'): array {
    $users = parent
      ::select('*')
      ::from(self::TABLE)
      ::where('id', 1)
      ::group_start()
        ::or_like('name', $name) // `name` LIKE '%john%' ESCAPE '!'
        ::or_like('name', $name) // `name` LIKE '%john%' ESCAPE '!'
        ::or_like('name', $name, 'before') // `name` LIKE '%john' ESCAPE '!'
        ::or_like('name', $name, 'after') // `name` LIKE 'john%' ESCAPE '!'
        ::or_like('name', $name, 'both') // `name` LIKE '%john%' ESCAPE '!'
      ->group_end()
      ->get()
      ->result_array();
    Logger::debug(parent::last_query());
    return $users;
  }

   public function transactionTest() {

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