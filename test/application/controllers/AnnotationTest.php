<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\AccessControl;
use \X\Util\Logger;
class AnnotationTest extends AppController
{

  /**
   * @AccessControl(allowLoggedin=false)
   */
  public function index()
  {
    parent::responseTemplate('login');
  }
}