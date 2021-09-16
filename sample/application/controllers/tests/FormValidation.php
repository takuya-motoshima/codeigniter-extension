<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Util\Logger;
use \X\Rekognition\Client;

class FormValidation extends AppController {
  public function index() {
    try {
      $this->form_validation
        ->set_data([
          'dir1' => '/',
          'dir2' => '/abc',
          'dir3' => '/sab_',
          'dir4' => '/abc/abc/',
          'dir5' => '/sad/dfsd',
          'dir6' => 'null',
          'dir7' => '/dsf/dfsdf/dsfsf/sdfds',
          'dir8' => '/e3r/343/8437',
          'dir9' => '/4333/32#'
        ])
        ->set_rules('dir1', 'dir1', 'directory_path')
        ->set_rules('dir2', 'dir2', 'directory_path')
        ->set_rules('dir3', 'dir3', 'directory_path')
        ->set_rules('dir4', 'dir4', 'directory_path')
        ->set_rules('dir5', 'dir5', 'directory_path')
        ->set_rules('dir6', 'dir6', 'directory_path')
        ->set_rules('dir7', 'dir7', 'directory_path')
        ->set_rules('dir8', 'dir8', 'directory_path')
        ->set_rules('dir9', 'dir9', 'directory_path');
      if ($this->form_validation->run() != false) {
        // put your code here
        Logger::print('There are no errors.');
      } else {
        Logger::print('Error message: ', $this->form_validation->error_array());
      }
    } catch (\Throwable $e) {
      Logger::print($e->getMessage());
    }
  }
}