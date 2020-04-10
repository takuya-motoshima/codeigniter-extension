<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\Access;
use \X\Util\Logger;

class Signin extends AppController {

  /**
   * @Access(allow_login=false, allow_logoff=true)
   */
  public function index() {
    parent::view('signin');
  }
}