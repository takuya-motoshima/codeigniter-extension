<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Util\Logger;

class TestModel extends \AppModel {

  const TABLE = 'test';

  public function getAll(): array {
    return parent
      ::from(self::TABLE)
      ::get()
      ->result_array();
  }

  public function saveRows(array $rows) {
    return parent
      ::set_insert_batch($rows)
      ::insert_on_duplicate_update_batch(self::TABLE);
  }

  public function saveRow(array $row) {
    return parent
      ::set($row)
      ::insert_on_duplicate_update(self::TABLE);
  }

  public function addRow(array $row) {
    return parent
      ::set($row)
      ::insert(self::TABLE);
  }

  public function deleteRow(string $thing) {
    return parent
      ::where('thing', $thing)
      ::delete(self::TABLE);
  }

  public function updateRow(string $fromThing, string $toThing) {
    return parent
      ::set('thing', $toThing)
      ::where('thing', $fromThing)
      ::update(self::TABLE);
  }
}