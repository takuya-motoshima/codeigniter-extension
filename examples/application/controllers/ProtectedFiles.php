<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\Access;
class ProtectedFiles extends AppController {

  /**
   * @Access(allow_login=true, allow_logoff=true)
   */
  public function index() {
    parent::internalRedirect('/protected_files/myfile.jpg');
  }
}