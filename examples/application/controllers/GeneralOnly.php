<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\Access;
use \X\Util\Logger;
class GeneralOnly extends AppController {

  /**
   * @Access(allow_login=true, allow_logoff=false, allow_role="general")
   */
  public function index() {
    parent::view('generalOnly');
  }
}