<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\Access;
use \X\Util\Logger;

class DotenvTest extends AppController {

  /**
   * @Access(allow_login=true, allow_logoff=true)
   */
  public function index() {
    Logger::print($_ENV);
  }
}