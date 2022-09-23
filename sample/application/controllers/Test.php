<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Util\Logger;
use \X\Util\Cipher;
use \X\Util\ImageHelper;

class Test extends AppController {
  public function password_hash_test() {
    $password = 'password';
    $passwordHash = Cipher::encode_sha256($password);
    Logger::print('$password=', $password);
    Logger::print('$passwordHash=', $passwordHash);
  }
  
  public function image_writing_test() {
    $filePath = FCPATH . 'upload/test.png';
    $dataUrl = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkCAIAAAD/gAIDAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAAEnQAABJ0Ad5mH3gAAAA0SURBVHhe7cEBDQAAAMKg909tDjcgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAIAbNXWUAAEE/b5iAAAAAElFTkSuQmCC';
    ImageHelper::putBase64($dataUrl, $filePath);
    Logger::print("Write {$filePath}");
  }

  public function error() {
    throw new \RuntimeException();
  }

  public function form_validation_test() {
    $data = [
      'hostname1' => 'example.com',
      'hostname2' => 'localhost',
      'hostname3' => 'c-61-123-45-67.hsd1.co.comcast.net',
      'hostname4' => 'example',
    ];
    $isValid = $this->form_validation
      ->set_data($data)
      ->set_rules('hostname1', 'hostname1', 'required|hostname')
      ->set_rules('hostname2', 'hostname2', 'required|hostname')
      ->set_rules('hostname3', 'hostname3', 'required|hostname')
      ->set_rules('hostname4', 'hostname4', 'required|hostname')
      ->run();
    Logger::print('$isValid=', $isValid ? 1 : 0);
    $error = $this->form_validation->error_array();
    Logger::print('$error=', $error);
  }
}