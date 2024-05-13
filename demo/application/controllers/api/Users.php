<?php
use \X\Annotation\Access;
use \X\Util\Logger;

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
      parent::error($e->getMessage(), 400);
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
      parent::error($e->getMessage(), 400);
    }
  }

  /**
   * @Access(allow_login=true, allow_logoff=false, allow_role="admin,member")
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
      $loginUserId = $_SESSION[SESSION_NAME]['id'];
      $data = $this->UserModel->paginate(
        $this->input->get('start'),
        $this->input->get('length'),
        $this->input->get('order'),
        $this->input->get('dir'),
        $this->input->get('search'),
        $loginUserId
      );
      $data['draw'] = $this->input->get('draw');
      parent::set($data)::json();
    } catch (\Throwable $e) {
      Logger::error($e);
      parent::error($e->getMessage(), 400);
    }
  }

  /**
   * @Access(allow_login=true, allow_logoff=false, allow_role="admin,member")
   */
  public function post() {
    try {
      $set = $this->input->post();
      $this->formValidation($set, 'create');
      $this->UserModel->createUser($set['user']);
      $this->UserLogModel->createUserLog($_SESSION[SESSION_NAME]['name'], 'Created user ' . $set['user']['name']);
      parent::set(true)::json();
    } catch (\Throwable $e) {
      Logger::error($e);
      parent::error($e->getMessage(), 400);
    }
  }

  /**
   * @Access(allow_login=true, allow_logoff=false, allow_role="admin,member")
   */
  public function emailExists() {
    try {
      $exists = $this->UserModel->emailExists(
        $this->input->get('user')['email'],
        $this->input->get('excludeUserId') ?? null);
      parent::set(['valid' => !$exists])::json();
    } catch (\Throwable $e) {
      Logger::error($e);
      parent::error($e->getMessage(), 400);
    }
  }

  /**
   * @Access(allow_login=true, allow_logoff=false, allow_role="admin,member")
   */
  public function get(int $userId) {
    try {
      parent::set($this->UserModel->getUserById($userId))::json();
    } catch (\Throwable $e) {
      Logger::error($e);
      parent::error($e->getMessage(), 400);
    }
  }

  /**
   * @Access(allow_login=true, allow_logoff=false, allow_role="admin,member")
   */
  public function put(int $userId) {
    try {
      $set = $this->input->put();
      $this->formValidation($set, 'update');
      $this->UserModel->updateUser($userId, $set['user']);
      $this->UserLogModel->createUserLog($_SESSION[SESSION_NAME]['name'], 'Updated User ' . $set['user']['name']);
      parent::set(true)::json();
    } catch (UserNotFoundException $e) {
      parent::set('error', 'userNotFound')::json();
    } catch (\Throwable $e) {
      Logger::error($e);
      parent::error($e->getMessage(), 400);
    }
  }

  /**
   * @Access(allow_login=true, allow_logoff=false, allow_role="admin,member")
   */
  public function delete(int $userId) {
    try {
      $userName = $this->UserModel
        ->select('name')
        ->where('id', $userId)
        ->get()
        ->row_array()['name'] ?? null;
      $this->UserModel->deleteUser($userId);
      $this->UserLogModel->createUserLog($_SESSION[SESSION_NAME]['name'], "User {$userName} is deleted");
      parent::set(true)::json();
    } catch (UserNotFoundException $e) {
      parent::set('error', 'userNotFound')::json();
    } catch (\Throwable $e) {
      Logger::error($e);
      parent::error($e->getMessage(), 400);
    }
  }

  /**
   * @Access(allow_login=true, allow_logoff=false)
   */
  public function passwordSecurityCheck() {
    try {
      $set = $this->input->get();
      // Logger::debug('$set=', $set);
      $this->form_validation
        ->set_data($set)
        ->set_rules('user[password]', 'user[password]', 'required');
      if (!$this->form_validation->run())
        throw new \RuntimeException('Invalid parameter');
      parent
        ::set('valid', $this->UserModel->passwordSecurityCheck($_SESSION[SESSION_NAME]['id'], $set['user']['password']))
        ::json();
    } catch (\Throwable $e) {
      Logger::error($e);
      parent::error($e->getMessage(), 400);
    }
  }

  /**
   * @Access(allow_login=true, allow_logoff=false)
   */
  public function updateProfile() {
    try {
      $set = $this->input->put();
      // Logger::debug('$set=', $set);
      $this->formValidation($set, 'updateProfile');
      $this->UserModel->updateUser($_SESSION[SESSION_NAME]['id'], $set['user']);
      $this->UserLogModel->createUserLog($_SESSION[SESSION_NAME]['name'], 'Updated profile');
      parent::set(true)::json();
    } catch (UserNotFoundException $e) {
      parent::set('error', 'userNotFound')::json();
    } catch (\Throwable $e) {
      Logger::error($e);
      parent::error($e->getMessage(), 400);
    }
  }

  private function formValidation(array $set, string $mode) {
    $this->form_validation
      ->set_data($set)
      // ->set_rules('user[role]', 'user[role]', 'required|in_list[admin,member]')
      ->set_rules('user[email]', 'user[email]', 'required')
      ->set_rules('user[name]', 'user[name]', 'required')
      ->set_rules('user[icon]', 'user[icon]', 'required|regex_match[/^data:image\/[a-z]+;base64,[a-zA-Z0-9\/\+=]+$/]');
    if ($mode === 'create' || $mode === 'update')
      $this->form_validation->set_rules('user[role]', 'user[role]', 'required|in_list[admin,member]');
    if ($mode === 'create' || !empty($set['user']['changePassword']))
      $this->form_validation->set_rules('user[password]', 'user[password]', 'required|min_length[8]|max_length[128]');
    if (!$this->form_validation->run()) {
      Logger::debug('error=', $this->form_validation->error_array());
      throw new \RuntimeException('Invalid parameter');
    }
  }
}