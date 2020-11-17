<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use X\Util\Logger;

/**
 * @see \X\Controller\Controller
 */
abstract class AppController extends \X\Controller\Controller {

  /**
   * @see \X\Controller\Controller::beforeResponse()
   */
  protected function beforeResponse(string $referer) {}

  /**
   * @see \X\Controller\Controller::beforeResponseJson()
   */
  protected function beforeResponseJson(string $referer) {}

  /**
   * @see \X\Controller\Controller::beforeResponseView()
   */
  protected function beforeResponseView(string $referer) {}

  /**
   * @see \X\Controller\Controller::beforeResponseHtml()
   */
  protected function beforeResponseHtml(string $referer) {}

  /**
   * @see \X\Controller\Controller::beforeResponseJs()
   */
  protected function beforeResponseJs(string $referer) {}

  /**
   * @see \X\Controller\Controller::beforeResponseText()
   */
  protected function beforeResponseText(string $referer) {}

  /**
   * @see \X\Controller\Controller::beforeDownload()
   */
  protected function beforeDownload(string $referer) {}

  /**
   * @see \X\Controller\Controller::beforeResponseImage()
   */
  protected function beforeResponseImage(string $referer) {}

  /**
   * @see \X\Controller\Controller::beforeInternalRedirect()
   */
  protected function beforeInternalRedirect(string $referer) {}
}