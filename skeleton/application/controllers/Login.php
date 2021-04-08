<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\Access;
use \X\Util\Logger;

class Login extends AppController {

  /**
   * @Access(allow_login=false, allow_logoff=true)
   */
  public function index() {
    parent::view('login');
  }
}