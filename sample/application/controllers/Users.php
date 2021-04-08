<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\Access;
use \X\Util\Logger;

class Users extends AppController {

  /**
   * @Access(allow_login=true, allow_logoff=false)
   */
  public function index() {
    parent::view('users');
  }
}