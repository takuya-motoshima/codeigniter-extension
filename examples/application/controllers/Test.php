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

      // Added "human" and "insect"
      $this->TestModel->saveRows([
        [ 'thing' => 'human' ],
        [ 'thing' => 'insect' ],
      ]);
      Logger::print('Added "human" and "insect": ', $this->TestModel->getAll());

      // Update "human" to "squid"
      $this->TestModel->saveRow([ 'id' => 1, 'thing' => 'squid' ]);
      Logger::print('Update "human" to "squid": ', $this->TestModel->getAll());

      // Added "Lion"
      $this->TestModel->addRow([ 'thing' => 'lion' ]);
      Logger::print('Added "Lion": ', $this->TestModel->getAll());

      // Delete "insect"
      $this->TestModel->deleteRow('insect');
      Logger::print('Delete "insect": ', $this->TestModel->getAll());

      // Update "squid" to "whale"
      $this->TestModel->updateRow('squid', 'whale');
      Logger::print('Update "squid" to "whale": ', $this->TestModel->getAll());

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
}