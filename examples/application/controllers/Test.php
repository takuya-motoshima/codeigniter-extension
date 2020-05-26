<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\Access;
use \X\Util\Logger;

class Test extends AppController {

  protected $model = 'TestModel';

  /**
   * @Access(allow_login=true, allow_logoff=true)
   */
  public function index() {

    try {

      Logger::print('Transaction start');

      // Transaction start
      $this->TestModel->trans_begin();

      // Added "Human" and "insect"
      $this->TestModel->saveRows([
        [ 'thing' => 'Human' ],
        [ 'thing' => 'insect' ],
      ]);
      Logger::print('Added "Human" and "insect": ', $this->TestModel->getAll());

      // Update "Human" to "Squid"
      $this->TestModel->saveRow([ 'id' => 1, 'thing' => 'Squid' ]);
      Logger::print('Update "Human" to "Squid": ', $this->TestModel->getAll());

      // Added "Lion"
      $this->TestModel->addRow([ 'thing' => 'Lion' ]);
      Logger::print('Added "Lion": ', $this->TestModel->getAll());

      // Delete "insect"
      $this->TestModel->deleteRow('insect');
      Logger::print('Delete "insect": ', $this->TestModel->getAll());

      // Update "Squid" to "Whale"
      $this->TestModel->updateRow('Squid', 'Whale');
      Logger::print('Update "Squid" to "Whale": ', $this->TestModel->getAll());

      // Undo
      $this->TestModel->trans_rollback();
      // $this->TestModel->trans_commit();

      // Search test table after rollback
      Logger::print('Test table rolled back: ', $this->TestModel->getAll());
    } catch (\Throwable $e) {
      $this->TestModel->trans_rollback();
      Logger::print($e->getMessage());
      throw $e;
    }
  }

  public function getUsingSubquery() {
    $results = $this->TestModel->getUsingSubquery();
    Logger::print($results);
  }
}