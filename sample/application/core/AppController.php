<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use X\Util\Logger;
require_once(dirname(__FILE__) . '/../exception/UserNotFoundException.php');

abstract class AppController extends \X\Controller\Controller {
  protected function beforeResponse(string $referer) {}
  protected function beforeResponseJson(string $referer) {}
  protected function beforeResponseView(string $referer) {
    if (isset($_SESSION[SESSION_NAME]))
      parent::set(SESSION_NAME, $_SESSION[SESSION_NAME]);
  }
  protected function beforeResponseHtml(string $referer) {}
  protected function beforeResponseJs(string $referer) {}
  protected function beforeResponseText(string $referer) {}
  protected function beforeDownload(string $referer) {}
  protected function beforeResponseImage(string $referer) {}
  protected function beforeInternalRedirect(string $referer) {}
}