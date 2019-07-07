<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\AccessControl;
class Dashboard extends AppController
{

  /**
   * @AccessControl(allow_login_user=true, allow_logoff_user=false)
   */
  public function index()
  {
    parent::responseTemplate('dashboard');
  }
}