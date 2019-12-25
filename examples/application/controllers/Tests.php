<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use \X\Annotation\Access;
use \X\Util\Logger;
use \X\Util\Cipher;
use \X\Util\ImageHelper;
class Tests extends AppController {

  public function index() {
    ini_set('display_errors', 0);
  }
}