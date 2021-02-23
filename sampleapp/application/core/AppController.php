<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use X\Util\Logger;
abstract class AppController extends \X\Controller\Controller {

  // protected function beforeResponse(string $referer) {
  //   Logger::debug('$referer=', $referer);
  //   $this->setCorsHeader('*');
  // }

  protected function beforeResponseView(string $referer) {
    if (isset($_SESSION['user'])) {
      parent::set('user', $_SESSION['user']);
    }
  }
}