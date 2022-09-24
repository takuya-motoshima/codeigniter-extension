<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\Access;
use \X\Util\Logger;

class Users extends AppController {
  protected $model = 'UserModel';

  /**
   * @Access(allow_login=false, allow_logoff=true)
   */
  public function login() {
    parent::view('login');
  }

  /**
   * @Access(allow_login=true, allow_logoff=false)
   */
  public function index() {
    parent::view('users');
  }

  /**
   * @Access(allow_login=true, allow_logoff=false)
   */
  public function personal() {
    parent
      ::set('user', $this->UserModel->getUserById($_SESSION[SESSION_NAME]['id']))
      ::view('personal');
  }

  /**
   * @Access(allow_login=true, allow_logoff=false)
   */
  public function editPersonal() {
    parent
      ::set('user', $this->UserModel->getUserById($_SESSION[SESSION_NAME]['id']))
      ::view('edit-personal');
  }
}