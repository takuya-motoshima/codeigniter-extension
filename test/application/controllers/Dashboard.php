<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\AccessControl;
class Dashboard extends AppController
{

  /**
   * @AccessControl(allowLoggedin=true, allowLoggedoff=false)
   */
  public function index()
  {
    parent::responseTemplate('dashboard');
  }
}