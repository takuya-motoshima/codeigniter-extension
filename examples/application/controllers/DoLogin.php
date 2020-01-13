<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\Access;
use \X\Util\Logger;
class DoLogin extends AppController {

  /**
   * @Access(allow_login=false, allow_logoff=true)
   */
  public function index() {
    $_SESSION['user'] = [
      'id' => $this->input->get('id'),
      'role' => $this->input->get('role')
    ];
    Logger::d('$_SESSION=', $_SESSION);
    redirect('/');
  }
}