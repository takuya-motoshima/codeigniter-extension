<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\AccessControl;
use \X\Util\Logger;
class Login extends AppController
{

  /**
   * @AccessControl(allow_login_user=false, allow_logoff_user=true)
   */
  public function index()
  {
    parent::responseTemplate('login');
  }

  /**
   * @AccessControl(allow_login_user=false, allow_logoff_user=true)
   */
  public function do_login() 
  {
    $_SESSION['user'] = $this->input->get('username');
    redirect('/');
  }
}