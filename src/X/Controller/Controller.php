<?php
namespace X\Controller;
use X\Util\HttpResponse;
use X\Util\Loader;

abstract class Controller extends \CI_Controller {
  protected $model;
  protected $library;
  protected $httpResponse;

  public function __construct() {
    parent::__construct();
    Loader::model($this->model);
    Loader::library($this->library);
    // $this->load->helper('url');
    $this->httpResponse = new HttpResponse();
  }

  /**
   * Sets the CORS header.
   * ```php
   * // Allow all origins
   * parent::setCorsHeader('*');
   *
   * // Allow a specific single origin
   * parent::setCorsHeader('http://www.example.jp');
   *   
   * // Allow specific multiple origins
   * parent::setCorsHeader('http://www.example.jp https://www.example.jp http://sub.example.jp');
   *
   * // To set the same Access-Control-Allow-Origin for all responses, use the hook point called before the response
   * // core/AppController.php: 
   * abstract class AppController extends \X\Controller\Controller {
   *   protected function beforeResponse(string $referer) {
   *     $this->setCorsHeader('*');
   *   }
   * }
   * ```
   */
  protected function setCorsHeader(string $origin = '*') {
    $this->httpResponse->setCorsHeader($origin);
    return $this;
  }

  /**
   * Set response.
   */
  protected function set($key, $value = null) {
    func_num_args() === 1 ? $this->httpResponse->set($key) : $this->httpResponse->set($key, $value);
    return $this;
  }

  /**
   * Clear response.
   */
  protected function clear() {
    $this->httpResponse->clear($key, $value);
    return $this;
  }

  /**
   * Set status.
   */
  protected function status(int $status) {
    $this->httpResponse->status($status);
    return $this;
  }

  /**
   * Response JSON.
   */
  protected function json(bool $forceObject = false, bool $prettyrint = false) {
    $this->beforeResponse($this->getReferer());
    $this->beforeResponseJson($this->getReferer());
    $this->httpResponse->json($forceObject, $prettyrint);
  }

  /**
   * Response HTML.
   */
  protected function html(string $html) {
    $this->beforeResponse($this->getReferer());
    $this->beforeResponseHtml($this->getReferer());
    $this->httpResponse->html($html);
  }

  /**
   * Response template.
   */
  protected function view(string $path) {
    $this->beforeResponse($this->getReferer());
    $this->beforeResponseView($this->getReferer());
    $this->httpResponse->view($path);
  }

  /**
   * Response js.
   */
  protected function js(string $js) {
    $this->beforeResponse($this->getReferer());
    $this->beforeResponseJs($this->getReferer());
    $this->httpResponse->js($js);
  }

  /**
   * Response text.
   */
  protected function text(string $text) {
    $this->beforeResponse($this->getReferer());
    $this->beforeResponseText($this->getReferer());
    $this->httpResponse->text($text);
  }

  /**
   * Response download.
   */
  protected function download(string $file, string $data = '', bool $mime = FALSE) {
    $this->beforeResponse($this->getReferer());
    $this->beforeDownload($this->getReferer());
    $this->httpResponse->download($file, $data, $mime);
  }

  /**
   * Response image.
   */
  protected function image(string $path) {
    $this->beforeResponse($this->getReferer());
    $this->beforeResponseImage($this->getReferer());
    $this->httpResponse->image($path);
  }

  /**
   * Internal redirect.
   */
  public function internalRedirect(string $internalRedirectPath) {
    $this->beforeResponse($this->getReferer());
    $this->beforeInternalRedirect($this->getReferer());
    $this->httpResponse->internalRedirect($internalRedirectPath);
  }

  /**
   * Response error.
   */
  protected function error(string $message, int $status = 500, bool $forceJsonResponse = false) {
    $this->httpResponse->error($message, $status, $forceJsonResponse);
  }

  /**
   * Get referrer.
   */
  private function getReferer() {
    return !empty($_SERVER['HTTP_REFERER'])
      ? $_SERVER['HTTP_REFERER']
      : (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  }

  /**
   * Before response.
   */
  protected function beforeResponse(string $referer) {}

  /**
   * Before response JSON.
   */
  protected function beforeResponseJson(string $referer) {}

  /**
   * Before response Template.
   */
  protected function beforeResponseView(string $referer) {}

  /**
   * Before response HTML.
   */
  protected function beforeResponseHtml(string $referer) {}

  /**
   * Before response JS.
   */
  protected function beforeResponseJs(string $referer) {}

  /**
   * Before response Text.
   */
  protected function beforeResponseText(string $referer) {}

  /**
   * Before download.
   */
  protected function beforeDownload(string $referer) {}

  /**
   * Before response json.
   */
  protected function beforeResponseImage(string $referer) {}

  /**
   * Before response json.
   */
  protected function beforeInternalRedirect(string $referer) {}
}