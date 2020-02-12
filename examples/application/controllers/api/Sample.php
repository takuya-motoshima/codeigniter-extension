<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Util\Logger;
use const \X\Constant\HTTP_BAD_REQUEST;
use const \X\Constant\HTTP_CREATED;
use const \X\Constant\HTTP_NO_CONTENT;

class Sample extends AppController {

  protected $model = 'SampleModel';

  public function get(string $id) {

    try {
      parent
        ::setCorsHeader('https://github.com')
        ::set($this->SampleModel->getById((int) $id))
        ::json();
    } catch (\Throwable $e) {
      parent::error($e->getMessage(), HTTP_BAD_REQUEST);
      exit;
    }
  }

  public function post() {

    try {

      // Validation
      $this->form_validation
        ->set_data($this->input->post())
        ->set_rules('name', 'name', 'required');
      if (!$this->form_validation->run()) {
        $error = print_r($this->form_validation->error_array(), true);
        Logger::error($error);
        return parent::error($error, HTTP_BAD_REQUEST);
      }

      // Add data
      $id = $this->SampleModel->add($this->input->post());

      // Response
      parent
        ::status(HTTP_CREATED)
        ::set($this->SampleModel->getById($id))
        ::json();
    } catch (\Throwable $e) {
      parent::error($e->getMessage(), HTTP_BAD_REQUEST);
    }
  }

  public function put(string $id) {

    try {

      // Validation
      $this->form_validation
        ->set_data($this->input->put())
        ->set_rules('name', 'name', 'required');
      if (!$this->form_validation->run()) {
        $error = print_r($this->form_validation->error_array(), true);
        Logger::error($error);
        return parent::error($error, HTTP_BAD_REQUEST);
      }

      // Update data
      $this->SampleModel->updateById((int) $id, $this->input->put());

      // Response
      parent
        ::status(HTTP_NO_CONTENT)
        ::set($this->SampleModel->getById($id))
        ::json();
    } catch (\Throwable $e) {
      parent::error($e->getMessage(), HTTP_BAD_REQUEST);
    }
  }

  public function delete(string $id) {

    try {

      // Delete data
      $this->SampleModel->deleteById((int) $id);

      // Response
      parent
        ::status(HTTP_NO_CONTENT)
        ::json();
    } catch (\Throwable $e) {
      parent::error($e->getMessage(), HTTP_BAD_REQUEST);
    }
  }
}