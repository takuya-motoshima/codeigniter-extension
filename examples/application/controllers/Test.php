<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\Access;
use \X\Util\Logger;
use \X\Util\Cipher;

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

  public function cipher() {
    Logger::print(Cipher::encode_sha256('tiger'));// 1583d0f164625326e8c78c008c53a6ad9a2d21556e3423abef12511bf6bf3753
    Logger::print(Cipher::encode_sha256('tiger', uniqid()));// 2fc96f26120bb333ada08609bb4ef009be4b20f2fa37468b05d5bacf885453fa
    Logger::print(Cipher::encode_sha256('tiger', uniqid()));// 066bf68b8150e46b5d77f088d00c125c7127f751dab5da91967f77363062e056
  }
}