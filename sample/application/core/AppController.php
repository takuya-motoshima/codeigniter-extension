<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use X\Util\Logger;
abstract class AppController extends \X\Controller\Controller {

  /**
   * Before response
   *
   * @param  string $referer
   * @return void
   */
  protected function beforeResponse(string $referer) {}

  /**
   * Before response JSON
   *
   * @param  string $referer
   * @return void
   */
  protected function beforeResponseJson(string $referer) {}

  /**
   * Before response Template
   * 
   * @param  string $referer
   * @return void
   */
  protected function beforeResponseView(string $referer) {
    if (isset($_SESSION[SESSION_NAME]))
      parent::set(SESSION_NAME, $_SESSION[SESSION_NAME]);
  }


  /**
   * Before response HTML
   *
   * @param  string $referer
   * @return void
   */
  protected function beforeResponseHtml(string $referer) {}

  /**
   * Before response JS
   *
   * @param  string $referer
   * @return void
   */
  protected function beforeResponseJs(string $referer) {}

  /**
   * Before response Text
   *
   * @param  string $referer
   * @return void
   */
  protected function beforeResponseText(string $referer) {}

  /**
   * Before download
   *
   * @param  string $referer
   * @return void
   */
  protected function beforeDownload(string $referer) {}

  /**
   * Before response json
   *
   * @param  string $referer
   * @return void
   */
  protected function beforeResponseImage(string $referer) {}

  /**
   * Before response json
   *
   * @param  string $referer
   * @return void
   */
  protected function beforeInternalRedirect(string $referer) {}
}