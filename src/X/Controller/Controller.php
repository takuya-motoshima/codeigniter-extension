<?php
/**
 * Base controller class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Controller;
use X\Util\HttpResponse;
use X\Util\Loader;

abstract class Controller extends \CI_Controller {

  /**
   * @var string|string[] $model
   */
  protected $model;

  /**
   * @var string|string[] $library
   */
  protected $library;

  /**
   * @var HttpResponse $httpResponse
   */
  protected $httpResponse;

  /**
   * Construct
   */
  public function __construct() {
    parent::__construct();
    Loader::model($this->model);
    Loader::library($this->library);
    // $this->load->helper('url');
    $this->httpResponse = new HttpResponse();
  }

  /**
   * Sets the CORS header
   *
   * eg.
   *  - Allow all origins
   *      parent::setCorsHeader('*');
   *
   *  - Allow a specific single origin
   *      parent::setCorsHeader('http://www.example.jp');
   *   
   *  - Allow specific multiple origins
   *      parent::setCorsHeader('http://www.example.jp https://www.example.jp http://sub.example.jp');
   *
   *  - To set the same Access-Control-Allow-Origin for all responses, use the hook point called before the response
   *      // core/AppController.php: 
   *      abstract class AppController extends \X\Controller\Controller {
   *        protected function beforeResponse(string $referer) {
   *          $this->setCorsHeader('*');
   *        }
   *      }
   * 
   * @param string $origin
   * @return void
   */
  protected function setCorsHeader(string $origin = '*') {
    $this->httpResponse->setCorsHeader($origin);
    return $this;
  }

  /**
   * Set response
   *
   * @param  mixed $key
   * @param  mixed $value
   * @return object
   */
  protected function set($key, $value = null) {
    func_num_args() === 1 ? $this->httpResponse->set($key) : $this->httpResponse->set($key, $value);
    return $this;
  }

  /**
   * Clear response
   *
   * @return object
   */
  protected function clear() {
    $this->httpResponse->clear($key, $value);
    return $this;
  }


  /**
   * Set status
   *
   * @param  int $status
   * @return object
   */
  protected function status(int $status) {
    $this->httpResponse->status($status);
    return $this;
  }

  /**
   * Response JSON
   *
   * @param  bool $forceObject
   * @param  bool $pretty
   * @return void
   */
  protected function json(bool $forceObject = false, bool $prettyrint = false) {
    $this->beforeResponse($this->getReferer());
    $this->beforeResponseJson($this->getReferer());
    $this->httpResponse->json($forceObject, $prettyrint);
  }

  /**
   * Response HTML
   *
   * @param  string  $html
   * @return void
   */
  protected function html(string $html) {
    $this->beforeResponse($this->getReferer());
    $this->beforeResponseHtml($this->getReferer());
    $this->httpResponse->html($html);
  }

  /**
   * Response template
   *
   * @param  string $path
   * @return void
   */
  protected function view(string $path) {
    $this->beforeResponse($this->getReferer());
    $this->beforeResponseView($this->getReferer());
    $this->httpResponse->view($path);
  }

  /**
   * Response js
   *
   * @param  string $js
   * @return void
   */
  protected function js(string $js) {
    $this->beforeResponse($this->getReferer());
    $this->beforeResponseJs($this->getReferer());
    $this->httpResponse->js($js);
  }

  /**
   * Response text
   *
   * @param  string $text
   * @return void
   */
  protected function text(string $text) {
    $this->beforeResponse($this->getReferer());
    $this->beforeResponseText($this->getReferer());
    $this->httpResponse->text($text);
  }

  /**
   * Response download
   *
   * @param  string $file
   * @param  string $data
   * @param  bool $mime
   * @return void
   */
  protected function download(string $file, string $data = '', bool $mime = FALSE) {
    $this->beforeResponse($this->getReferer());
    $this->beforeDownload($this->getReferer());
    $this->httpResponse->download($file, $data, $mime);
  }

  /**
   * Response image
   *
   * @param  string $path
   * @return void
   */
  protected function image(string $path) {
    $this->beforeResponse($this->getReferer());
    $this->beforeResponseImage($this->getReferer());
    $this->httpResponse->image($path);
  }

  /**
   * Internal redirect
   *  
   * @param  string $internalRedirectPath
   * @return void
   */
  public function internalRedirect(string $internalRedirectPath) {
    $this->beforeResponse($this->getReferer());
    $this->beforeInternalRedirect($this->getReferer());
    $this->httpResponse->internalRedirect($internalRedirectPath);
  }

  /**
   * Response error
   *
   * @param  string $errorMessage
   * @param  int $status
   * @return void
   */
  protected function error(string $message, int $status = \X\Constant\HTTP_INTERNAL_SERVER_ERROR) {
    $this->httpResponse->error($message, $status);
  }

  private function getReferer() {
    return !empty($_SERVER['HTTP_REFERER'])
      ? $_SERVER['HTTP_REFERER']
      : (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  }

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
  protected function beforeResponseView(string $referer) {}


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