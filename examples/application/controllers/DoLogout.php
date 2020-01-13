<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\Access;
use \X\Util\Logger;
class DoLogout extends AppController {

  /**
   * @Access(allow_login=true, allow_logoff=false)
   */
  public function index() {
    unset ($_SESSION['user']);
    redirect('/');
  }
}