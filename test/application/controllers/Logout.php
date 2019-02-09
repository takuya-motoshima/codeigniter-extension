<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\AccessControl;
use \X\Util\Logger;
class Logout extends AppController
{

  /**
   * @AccessControl(allowLoggedin=true, allowLoggedoff=false)
   */
  public function index()
  {
    unset ($_SESSION['user']);
    redirect('/');
  }
}