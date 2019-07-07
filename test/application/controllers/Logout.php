<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\AccessControl;
use \X\Util\Logger;
class Logout extends AppController
{

  /**
   * @AccessControl(allow_login_user=true, allow_logoff_user=false)
   */
  public function index()
  {
    unset ($_SESSION['user']);
    redirect('/');
  }
}