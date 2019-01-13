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
abstract class Controller extends \CI_Controller
{

  /**
   * @var string|string[] $model
   */
  protected $model;

  /**
   * @var string|string[] $library
   */
  protected $library;

  /**
   * HttpResponse $response
   * @var [type]
   */
  private $response;

  /**
   * construct
   */
  public function __construct()
  {
    parent::__construct();
    Loader::model($this->model);
    Loader::library($this->library);
    $this->response = new HttpResponse();
  }

  /**
   * Set http status
   *
   * @param  int $status
   * @return object
   */
  protected function status(int $status)
  {
    $this->response->status($status);
    return $this;
  }

  /**
   * Set response
   *
   * @param  mixed $key
   * @param  mixed $value
   * @return object
   */
  protected function set($key, $value = null)
  {
    func_num_args() === 1 
      ? $this->response->set($key)
      : $this->response->set($key, $value);
    return $this;
  }

  /**
   * Clear response
   *
   * @return object
   */
  protected function clear()
  {
    $this->response->clear($key, $value);
    return $this;
  }

  /**
   * Response JSON
   *
   * Set the following variable to 'environment'
   *     baseUrl
   *
   * @param  bool $forceObject
   * @param  bool $pretty
   * @return void
   */
  protected function responseJson()
  {
    $this->beforeResponseJson();
    $this->response->json();
  }

  /**
   * 
   * Set response json option
   * 
   * @param int $option JSON_FORCE_OBJECT | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
   * @param bool $enabled
   * @return object
   */
  protected function setJsonOption(int $option, bool $enabled)
  {
    $this->response->jsonOption($option, $enabled);
    return $this;
  }

  /**
   * Response HTML
   *
   * @param  string  $source
   * @param  string $char
   * @return void
   */
  protected function responseHtml(string $source, string $char = 'utf-8')
  {
    $this->response->html($source, $char);
  }

  /**
   * Response HTML for template
   *
   * Set the following variable to 'environment'
   *     baseUrl
   *
   * @param  string $teamplatePath
   * @param  string $char
   * @return void
   */
  protected function responseTemplate(string $filePath, string $char = 'utf-8')
  {
    $this->beforeResponseTemplate($filePath);
    $this->response->template($filePath, $char);
  }

  /**
   * Response javascript
   *
   * @param  string $source
   * @param  string $char
   * @return void
   */
  protected function responseJavascript(string $source, string $char = 'UTF-8')
  {
    $this->response->javascript($source, $char);
  }

  /**
   * Response text
   *
   * @param  string $text
   * @param  string $char
   * @return void
   */
  protected function responseText(string $text, string $char = 'UTF-8')
  {
    $this->response->text($text, $char);
  }

  /**
   * Response download
   *
   * @param  string $fileMame
   * @param  string $data
   * @param  bool $mime
   * @return void
   */
  protected function responseDownload(string $fileMame, string $data = '', bool $mime = FALSE)
  {
    $this->response->download($fileMame, $data, $mime);
  }

  /**
   * Response image
   *
   * @param  string $filePath
   * @return void
   */
  protected function responseImage(string $filePath)
  {
    $this->response->image($filePath);
  }

  /**
   * Response error
   *
   * @param  string $errorMessage
   * @param  int $httStatus
   * @return void
   */
  protected function responseError(string $errorMessage, int $httStatus = \X\Constant\HTTP_INTERNAL_SERVER_ERROR)
  {
    $this->response->error($errorMessage, $httStatus);
  }

  /**
   * Before response json
   *
   * @param  int $responseType
   * @return void
   */
  protected function beforeResponseJson() {}

  /**
   * Before response template
   *
   * @param  int $responseType
   * @return void
   */
  protected function beforeResponseTemplate(string $filePath) {}
}