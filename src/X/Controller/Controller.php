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
   * HttpResponse $httpResponse
   * @var [type]
   */
  private $httpResponse;

  /**
   * @var array $data
   */
  private $data = [];

  /**
   * construct
   */
  public function __construct()
  {
    parent::__construct();
    Loader::model($this->model);
    Loader::library($this->library);
    $this->httpResponse = new HttpResponse();
  }

  /**
   * Set http status
   *
   * @param  int $status
   * @return object
   */
  protected function set_status(int $status)
  {
    $this->httpResponse->set_status($status);
    return $this;
  }

  /**
   * Set response data
   *
   * @param  mixed $key
   * @param  mixed $value
   * @return object
   */
  protected function set($key, $value = null)
  {
    func_num_args() === 1 
      ? $this->httpResponse->set($key)
      : $this->httpResponse->set($key, $value);
    return $this;
  }

  /**
   * Clear response data
   *
   * @return object
   */
  protected function clear()
  {
    $this->httpResponse->clear($key, $value);
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
  protected function response_json()
  // protected function response_json(bool $forceObject = false, bool $pretty = false, bool $unescapedSlashes = true, bool $unescapedUnicode = false)
  {
    $this->before_response_json();
    $this->httpResponse->json();
    // $this->httpResponse->json($forceObject, $pretty, $unescapedSlashes, $unescapedUnicode);
  }

  /**
   * 
   * Set response json option
   * 
   * @param int $option JSON_FORCE_OBJECT | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
   * @param bool $enabled
   * @return void
   */
  protected function setResponseJsonOption(int $option, bool $enabled)
  {
    $this->httpResponse->jsonOption($option, $enabled);
  }

  /**
   * Response HTML
   *
   * @param  string  $html
   * @param  string $char
   * @return void
   */
  protected function response_html(string $html, string $char = 'utf-8')
  {
    $this->httpResponse->html($html, $char);
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
  protected function response_template(string $templatePath, string $char = 'utf-8')
  {
    $this->before_response_template($templatePath);
    $this->httpResponse->template($templatePath, $char);
  }

  /**
   * Response javascript
   *
   * @param  string $code
   * @param  string $char
   * @return void
   */
  protected function response_javascript(string $code, string $char = 'UTF-8')
  {
    $this->httpResponse->javascript($code, $char);
  }

  /**
   * Response text
   *
   * @param  string $text
   * @param  string $char
   * @return void
   */
  protected function response_text(string $text, string $char = 'UTF-8')
  {
    $this->httpResponse->text($text, $char);
  }

  /**
   * Response download
   *
   * @param  string $file_name
   * @param  string $data
   * @param  bool $set_mime
   * @return void
   */
  protected function response_download(string $file_name, string $data = '', bool $set_mime = FALSE)
  {
    $this->httpResponse->download($file_name, $data, $set_mime);
  }

  /**
   * Response image
   *
   * @param  string $image_path
   * @return void
   */
  protected function response_image(string $image_path)
  {
    $this->httpResponse->image($image_path);
  }

  /**
   * Response error
   *
   * @param  string $errorMessage
   * @param  int $httStatus
   * @return void
   */
  protected function response_error(string $errorMessage, int $httStatus = \X\Constant\HTTP_INTERNAL_SERVER_ERROR)
  {
    $this->httpResponse->error($errorMessage, $httStatus);
  }

  /**
   * Before response json
   *
   * @param  int $responseType
   * @return void
   */
  protected function before_response_json() {}

  /**
   * Before response template
   *
   * @param  int $responseType
   * @return void
   */
  protected function before_response_template(string $templatePath) {}
}