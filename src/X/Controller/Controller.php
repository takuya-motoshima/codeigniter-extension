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
    $this->httpResponse = new HttpResponse();
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
    $this->beforeJson();
    $this->httpResponse->json($forceObject, $prettyrint);
  }

  /**
   * Response HTML
   *
   * @param  string  $html
   * @return void
   */
  protected function html(string $html) {
    $this->httpResponse->html($html);
  }

  /**
   * Response template
   *
   * @param  string $path
   * @return void
   */
  protected function view(string $path) {
    $this->beforeView();
    $this->httpResponse->view($path);
  }

  /**
   * Response js
   *
   * @param  string $js
   * @return void
   */
  protected function js(string $js) {
    $this->httpResponse->javascript($js);
  }

  /**
   * Response text
   *
   * @param  string $text
   * @return void
   */
  protected function text(string $text) {
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
    $this->httpResponse->download($file, $data, $mime);
  }

  /**
   * Response image
   *
   * @param  string $path
   * @return void
   */
  protected function image(string $path) {
    $this->httpResponse->image($path);
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

  /**
   * Before response json
   *
   * @param  int $responseType
   * @return void
   */
  protected function beforeJson() {}

  /**
   * Before response template
   *
   * @return void
   */
  protected function beforeView() {}
}