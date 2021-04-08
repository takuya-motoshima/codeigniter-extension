<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\Access;
use \X\Util\Logger;
use const \X\Constant\HTTP_BAD_REQUEST;
use const \X\Constant\HTTP_CREATED;
use const \X\Constant\HTTP_NO_CONTENT;

class Users extends AppController {

  protected $model = 'UserService';

  /**
   * Authenticate with username and password.
   * 
   * @Access(allow_login=false, allow_logoff=true)
   */
  public function login() {

    Logger::debug('Parameters:', $this->input->post());

    try {
      // Check input data.
      $this->form_validation
        ->set_data($this->input->post())
        ->set_rules('email', 'email', 'required')
        ->set_rules('password', 'password', 'required');
      if (!$this->form_validation->run()) {
        Logger::error($this->form_validation->error_array());
        return parent::set('error', 'input_error')::set('error_description', $this->form_validation->error_array())::json();
      }

      // Authentication.
      $res = $this->UserService->login($this->input->post('email'), $this->input->post('password'));

      // Login failure.
      if (!$res)
        return parent::set('error', 'cannot_authenticate')::json();

      // Login successful.
      parent::set($res)::json();
    } catch (\Throwable $e) {
      Logger::error($e);
      parent::error($e->getMessage(), HTTP_BAD_REQUEST);
    }
  }

  /**
   * @Access(allow_login=true, allow_logoff=false)
   */
  public function logout() {
    try {
      $this->UserService->logout();
      redirect('/login');
    } catch (\Throwable $e) {
      parent::error($e->getMessage(), HTTP_BAD_REQUEST);
    }
  }

  /**
   * @Access(allow_login=true, allow_logoff=false, allow_role="admin")
   */
  public function query() {

    // Logger::debug('Parameters:', $this->input->get());

    try {
      // Get data in the specified page number range.
      $data = $this->UserService->paginate(
        $this->input->get('start'),
        $this->input->get('length'),
        $this->input->get('order'),
        $this->input->get('dir'),
        $this->input->get('search'));
      $data['draw'] = $this->input->get('draw');
      parent
        ::set($data)
        ::json();
    } catch (\Throwable $e) {
      parent::error($e->getMessage(), HTTP_BAD_REQUEST);
    }
  }

  /**
   * @Access(allow_login=true, allow_logoff=false, allow_role="admin")
   */
  public function get(int $id) {
    try {
      parent
        ::set($this->UserService->getUserById($id))
        ::json();
    } catch (\Throwable $e) {
      parent::error($e->getMessage(), HTTP_BAD_REQUEST);
    }
  }

  /**
   * @Access(allow_login=true, allow_logoff=false, allow_role="admin")
   */
  public function post() {
    try {
      // Check input data.
      $this->form_validation
        ->set_data($this->input->post())
        ->set_rules('role', 'role', 'required|in_list[admin,member]')
        ->set_rules('email', 'email', 'required')
        ->set_rules('password', 'password', 'required');
      if (!$this->form_validation->run()) {
        Logger::error($this->form_validation->error_array());
        return parent::set('error', 'input_error')::set('error_description', $this->form_validation->error_array())::json();
      }
      $id = $this->UserService->addUser($this->input->post());
      parent
        ::status(HTTP_CREATED)
        ::set($id)
        ::json();
    } catch (\Throwable $e) {
      parent::error($e->getMessage(), HTTP_BAD_REQUEST);
    }
  }

  /**
   * @Access(allow_login=true, allow_logoff=false, allow_role="admin")
   */
  public function put(int $id) {

    Logger::debug('Parameters:', $this->input->put());

    try {
      // Check input data.
      $this->form_validation
        ->set_data($this->input->post())
        ->set_rules('role', 'role', 'required|in_list[admin,member]')
        ->set_rules('email', 'email', 'required');
      if (!$this->form_validation->run()) {
        Logger::error($this->form_validation->error_array());
        return parent::set('error', 'input_error')::set('error_description', $this->form_validation->error_array())::json();
      }
      $this->UserService->updateUser($id, $this->input->put());
      parent::status(HTTP_NO_CONTENT)::json();
    } catch (\Throwable $e) {
      parent::error($e->getMessage(), HTTP_BAD_REQUEST);
    }
  }

  /**
   * @Access(allow_login=true, allow_logoff=false, allow_role="admin")
   */
  public function delete(int $id) {
    try {
      $this->UserService->deleteUser($id);
      parent::status(HTTP_NO_CONTENT)::json();
    } catch (\Throwable $e) {
      parent::error($e->getMessage(), HTTP_BAD_REQUEST);
    }
  }
}