<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\AccessControl;
use \X\Util\Logger;
class AnnotationTest extends AppController
{

  /**
   * @AccessControl(allow_login_user=false)
   */
  public function index()
  {
    parent::responseTemplate('login');
  }
}