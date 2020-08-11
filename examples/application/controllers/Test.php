<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\Access;
use \X\Util\Logger;
use \X\Util\Cipher;
use \X\Util\FileHelper;

class Test extends AppController {

  protected $model = 'TestModel';

  public function index() {
    parent::view('test');
  }

  public function getDirectorySize() {
    $size = FileHelper::getDirectorySize([ APPPATH . 'test/animals', APPPATH . 'test/transport' ]);
    echo "Size: $size";

    $size = FileHelper::getDirectorySize(APPPATH . 'test/animals');
    echo "Size: $size";

    $size = FileHelper::getDirectorySize(APPPATH . 'test/transport');
    echo "Size: $size";
  }

  public function copyDirectory() {
    FileHelper::copyDirectory(APPPATH . 'test/animals', APPPATH . 'test/copyResult');
  }

  public function zipLibrary() {
    Logger::debug('Memory at start: ', floor(memory_get_usage() / (1024 * 1024)) .'MB');
    Logger::debug('Maximum memory at start: ', floor(memory_get_peak_usage() / (1024 * 1024)) .'MB');
    // Add archive file
    $targetDir = APPPATH . 'test';
    $files = FileHelper::find($targetDir . '/*');
    $this->load->library('zip');
    foreach ($files as $file) {
      $this->zip->add_data($file, file_get_contents($targetDir . '/' . $file));
    }
    // Create archive
    $archivePath = tempnam(sys_get_temp_dir(), uniqid());
    $this->zip->archive($archivePath);
    Logger::debug('Memory at end: ', floor(memory_get_usage() / (1024 * 1024)) .'MB');
    Logger::debug('Maximum memory at end: ', floor(memory_get_peak_usage() / (1024 * 1024)) .'MB');
    // Response
    $this->zip->download('test.zip');
  }


  public function zipStream() {
    Logger::debug('Memory at start: ', floor(memory_get_usage() / (1024 * 1024)) .'MB');
    Logger::debug('Maximum memory at start: ', floor(memory_get_peak_usage() / (1024 * 1024)) .'MB');
    // Add archive file
    $options = new ZipStream\Option\Archive();
    $options->setSendHttpHeaders(true);
    $fileopt = new ZipStream\Option\File();
    $fileopt->setMethod(ZipStream\Option\Method::STORE());
    $zip = new ZipStream\ZipStream('test.zip', $options);
    // Add archive file
    $targetDir = APPPATH . 'test';
    $files = FileHelper::find($targetDir . '/*');
    foreach($files as $file) {
      $zip->addFileFromPath($file, $targetDir . '/' . $file, $fileopt);
    }
    // Response
    $zip->finish();
    Logger::debug('Memory at end: ', floor(memory_get_usage() / (1024 * 1024)) .'MB');
    Logger::debug('Maximum memory at end: ', floor(memory_get_peak_usage() / (1024 * 1024)) .'MB');
  }


  public function subquery() {
    $results = $this->TestModel->getUsingSubquery();
    Logger::print($results);
  }

  public function transaction() {
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

  public function cipher() {
    Logger::print(Cipher::encode_sha256('tiger'));// 1583d0f164625326e8c78c008c53a6ad9a2d21556e3423abef12511bf6bf3753
    Logger::print(Cipher::encode_sha256('tiger', uniqid()));// 2fc96f26120bb333ada08609bb4ef009be4b20f2fa37468b05d5bacf885453fa
    Logger::print(Cipher::encode_sha256('tiger', uniqid()));// 066bf68b8150e46b5d77f088d00c125c7127f751dab5da91967f77363062e056
  }
}