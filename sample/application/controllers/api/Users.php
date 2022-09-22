<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\Access;
use \X\Util\Logger;
use const \X\Constant\HTTP_BAD_REQUEST;

class Users extends AppController {
  protected $model = [
    'UserModel',
    'UserLogModel'
  ];

  /**
   * @Access(allow_login=false, allow_logoff=true)
   */
  public function login() {
    try {
      $this->form_validation
        ->set_data($this->input->post())
        ->set_rules('email', 'email', 'required')
        ->set_rules('password', 'password', 'required');
      if (!$this->form_validation->run())
        throw new \RuntimeException('Invalid parameter');
      if (!$this->UserModel->login($this->input->post('email'), $this->input->post('password')))
        return parent::set(false)::json();
      $this->UserLogModel->createUserLog($_SESSION[SESSION_NAME]['name'], 'Logged in.');
      parent::set(true)::json();
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
      $this->UserLogModel->createUserLog($_SESSION[SESSION_NAME]['name'], 'Logged out.');
      $this->UserModel->logout();
      redirect('/');
    } catch (\Throwable $e) {
      Logger::error($e);
      parent::error($e->getMessage(), HTTP_BAD_REQUEST);
    }
  }

  /**
   * @Access(allow_login=true, allow_logoff=false, allow_role="admin")
   */
  public function query() {
    try {
      $this->form_validation
        ->set_data($this->input->get())
        ->set_rules('start', 'start', 'required|is_natural')
        ->set_rules('length', 'length', 'required|is_natural')
        ->set_rules('order', 'order', 'required')
        ->set_rules('dir', 'dir', 'required|in_list[asc,desc]');
      if (!$this->form_validation->run())
        throw new \RuntimeException('Invalid parameter');
      $data = $this->UserModel->paginate(
        $this->input->get('start'),
        $this->input->get('length'),
        $this->input->get('order'),
        $this->input->get('dir'),
        $this->input->get('search')
      );
      $data['draw'] = $this->input->get('draw');
      parent::set($data)::json();
    } catch (\Throwable $e) {
      Logger::error($e);
      parent::error($e->getMessage(), HTTP_BAD_REQUEST);
    }
  }

  /**
   * @Access(allow_login=true, allow_logoff=false, allow_role="admin")
   */
  public function post() {
    try {
      $set = $this->input->post();
      $this->formValidation($set, 'create');
      $this->UserModel->createUser($set['user']);
      parent::set(true)::json();
    } catch (\Throwable $e) {
      Logger::error($e);
      parent::error($e->getMessage(), HTTP_BAD_REQUEST);
    }
  }

  /**
   * @Access(allow_login=true, allow_logoff=false, allow_role="admin")
   */
  public function emailExists() {
    try {
      $exists = $this->UserModel->emailExists($this->input->get('user')['email']);
      parent::set(['valid' => !$exists])::json();
    } catch (\Throwable $e) {
      Logger::error($e);
      parent::error($e->getMessage(), HTTP_BAD_REQUEST);
    }
  }

  // /**
  //  * @Access(allow_login=true, allow_logoff=false, allow_role="admin")
  //  */
  // public function get(int $userId) {
  //   parent::set($this->UserModel->getUserById($userId))::json();
  // }

  // /**
  //  * @Access(allow_login=true, allow_logoff=false, allow_role="admin")
  //  */
  // public function put(int $userId) {
  //   $this->form_validation
  //     ->set_data($this->input->post())
  //     ->set_rules('role', 'role', 'required|in_list[admin,member]')
  //     ->set_rules('email', 'email', 'required');
  //   if (!$this->form_validation->run()) {
  //     Logger::error($this->form_validation->error_array());
  //     return parent::set('error', 'input_error')::set('error_description', $this->form_validation->error_array())::json();
  //   }
  //   $this->UserModel->updateUser($userId, $this->input->put());
  //   parent::status(HTTP_NO_CONTENT)::json();
  // }

  // /**
  //  * @Access(allow_login=true, allow_logoff=false, allow_role="admin")
  //  */
  // public function delete(int $userId) {
  //   $this->UserModel->deleteUser($userId);
  //   parent::status(HTTP_NO_CONTENT)::json();
  // }

  private function formValidation(array $set, string $mode) {
    $this->form_validation
      ->set_data($set)
      ->set_rules('user[role]', 'user[role]', 'required|in_list[admin,member]')
      ->set_rules('user[email]', 'user[email]', 'required')
      ->set_rules('user[name]', 'user[name]', 'required')
      ->set_rules('user[icon]', 'user[icon]', 'required|regex_match[/^data:image\/[a-z]+;base64,[a-zA-Z0-9\/\+=]+$/]');
    if ($mode === 'create' || !empty($set['user']['changePassword']))
      $this->form_validation->set_rules('user[password]', 'user[password]', 'required|min_length[8]|max_length[128]');
    if (!$this->form_validation->run()) {
      Logger::debug('error=', $this->form_validation->error_array());
      throw new \RuntimeException('Invalid parameter');
    }
  }
}