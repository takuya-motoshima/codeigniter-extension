<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Util\Logger;
use \X\Util\Cipher;
use \X\Util\ImageHelper;

class Test extends AppController {
  protected $model = 'UserModel';

  public function password_hash_test() {
    $password = 'password';
    $passwordHash = Cipher::encode_sha256($password);
    Logger::display('$password=', $password);
    Logger::display('$passwordHash=', $passwordHash);
  }
  
  public function image_writing_test() {
    $filePath = FCPATH . 'upload/test.png';
    $dataUrl = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkCAIAAAD/gAIDAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAAEnQAABJ0Ad5mH3gAAAA0SURBVHhe7cEBDQAAAMKg909tDjcgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAIAbNXWUAAEE/b5iAAAAAElFTkSuQmCC';
    ImageHelper::putBase64($dataUrl, $filePath);
    Logger::display("Write {$filePath}");
  }

  // public function error() {
  //   throw new \RuntimeException();
  // }

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
    Logger::display('$isValid=', $isValid ? 1 : 0);
    $error = $this->form_validation->error_array();
    Logger::display('$error=', $error);
  }

  /**
   * ```sh
   * CI_ENV=development php public/index.php test/log_test
   * ```
   */
  public function log_test() {
    Logger::debug('debug');
    Logger::info('info');
    Logger::error('error');
    Logger::display('display');
  }

  /**
   * ```sh
   * CI_ENV=development php public/index.php test/sanitize_sql_parameters
   * ```
   */
  public function sanitize_sql_parameters() {
    $this->UserModel
      ->where('id', 1)
      ->get()
      ->row_array();
    $sql = $this->UserModel->last_query();
    Logger::display($sql);// => SELECT * FROM `user` WHERE `id` = 1
  
    $this->UserModel
      ->where('id', '\'1\' OR id=2')
      ->get()
      ->row_array();
    $sql = $this->UserModel->last_query();
    Logger::display($sql);
    // => SELECT * FROM `user` WHERE `id` = '\'1\' OR id=2'

    $this->UserModel
      ->where('id', '\'1\' OR id=2', FALSE)
      ->get()
      ->row_array();
    $sql = $this->UserModel->last_query();
    Logger::display($sql);
    // => SELECT * FROM `user` WHERE id =  '1' OR id=2
  }

  public function get_ip() {
    $ip = $this->input->ip_address();
    Logger::display($ip);
  }
}