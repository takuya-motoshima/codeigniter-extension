<?php
use \X\Annotation\Access;
use \X\Util\Logger;

class Userlogs extends AppController {
  protected $model = 'UserLogModel';

  /**
   * @Access(allow_login=true, allow_logoff=false)
   */
  public function index() {
    parent
      ::set('usernameOptions', $this->UserLogModel->getUsernameOptions())
      ::view('userlogs');
  }
}