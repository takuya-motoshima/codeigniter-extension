<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Util\Logger;

class Dotenv extends AppController {
  public function index() {
    Logger::print($_ENV);
  }
}