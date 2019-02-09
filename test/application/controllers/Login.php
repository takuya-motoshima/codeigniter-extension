<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\AccessControl;
use \X\Util\Logger;
class Login extends AppController
{

  /**
   * @AccessControl(allowLoggedin=false, allowLoggedoff=true)
   */
  public function index()
  {
    parent::responseTemplate('login');
  }

  /**
   * @AccessControl(allowLoggedin=false, allowLoggedoff=true)
   */
  public function do_login() 
  {
    $_SESSION['user'] = $this->input->get('username');
    redirect('/');
  }
}