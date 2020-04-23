<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\Access;
use \X\Util\Logger;
use const \X\Constant\HTTP_BAD_REQUEST;
use const \X\Constant\HTTP_CREATED;
use const \X\Constant\HTTP_NO_CONTENT;

class User extends AppController {

  protected $model = 'UserModel';

  /**
   * @Access(allow_login=false, allow_logoff=true)
   */
  public function signin() {
    try {
      $this->form_validation
        ->set_data($this->input->post())
        ->set_rules('username', 'username', 'required|max_length[30]')
        ->set_rules('password', 'password', 'required|max_length[30]');
      if (!$this->form_validation->run()) {
        Logger::error($this->form_validation->error_string());
        return parent::error(print_r($this->form_validation->error_array(), true), HTTP_BAD_REQUEST);
      }
      $result = $this->UserModel->signin($this->input->post('username'), $this->input->post('password'));
      parent
        ::set($result)
        ::json();
    } catch (\Throwable $e) {
      Logger::error($e);
      parent::error($e->getMessage(), HTTP_BAD_REQUEST);
    }
  }

  /**
   * @Access(allow_login=true, allow_logoff=false)
   */
  public function signout() {
    try {
      $this->UserModel->signout();
      redirect('/signin');
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
        ::set($this->UserModel->getUserById($id))
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
      $this->form_validation
        ->set_data($this->input->post())
        ->set_rules('role', 'role', 'required|in_list[admin,user]')
        ->set_rules('username', 'username', 'required|max_length[30]')
        ->set_rules('password', 'password', 'required|max_length[30]');
      if (!$this->form_validation->run()) {
        return parent::error(print_r($this->form_validation->error_array(), true), HTTP_BAD_REQUEST);
      }

      $id = $this->UserModel->addUser($this->input->post());

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
    try {
      if (empty($this->input->put())) {
        throw new RuntimeException('Update data is undefined');
      }

      $this->form_validation->set_data($this->input->put());
      if (!empty($this->input->put('role'))) {
        $this->form_validation->set_rules('role', 'role', 'in_list[admin,user]');
      }
      if (!empty($this->input->put('username'))) {
        $this->form_validation->set_rules('username', 'username', 'min_length[1]|max_length[30]');
      }
      if (!empty($this->input->put('password'))) {
        $this->form_validation->set_rules('password', 'password', 'min_length[8]|max_length[30]');
      }
      if (!$this->form_validation->run()) {
        return parent::error(print_r($this->form_validation->error_array(), true), HTTP_BAD_REQUEST);
      }

      $this->UserModel->updateUser($id, $this->input->put());
      parent
        ::status(HTTP_NO_CONTENT)
        ::json();
    } catch (\Throwable $e) {
      parent::error($e->getMessage(), HTTP_BAD_REQUEST);
    }
  }

  /**
   * @Access(allow_login=true, allow_logoff=false, allow_role="admin")
   */
  public function delete(int $id) {
    try {
      $this->UserModel->deleteUser($id);
      parent
        ::status(HTTP_NO_CONTENT)
        ::json();
    } catch (\Throwable $e) {
      parent::error($e->getMessage(), HTTP_BAD_REQUEST);
    }
  }
}