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
   * @param  int $statusCode
   * @return object
   */
  protected function status(int $statusCode)
  {
    $this->response->status($statusCode);
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
    $this->before_response_json();
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
   * @param  string  $htmlCode
   * @param  string $char
   * @return void
   */
  protected function responseHtml(string $htmlCode, string $char = 'utf-8')
  {
    $this->response->html($htmlCode, $char);
  }

  /**
   * Response HTML for template
   *
   * Set the following variable to 'environment'
   *     baseUrl
   *
   * @param  string $templatePath
   * @param  string $char
   * @return void
   */
  protected function responseTemplate(string $templatePath, string $char = 'utf-8')
  {
    $this->beforeResponseTemplate($templatePath);
    $this->before_response_template($templatePath);
    $this->response->template($templatePath, $char);
  }

  /**
   * Response javascript
   *
   * @param  string $scriptCode
   * @param  string $char
   * @return void
   */
  protected function responseJavascript(string $scriptCode, string $char = 'UTF-8')
  {
    $this->response->javascript($scriptCode, $char);
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
   * @param  string $imagePath
   * @return void
   */
  protected function responseImage(string $imagePath)
  {
    $this->response->image($imagePath);
  }

  /**
   * Response error
   *
   * @param  string $errorMessage
   * @param  int $statusCode
   * @return void
   */
  protected function responseError(string $errorMessage, int $statusCode = \X\Constant\HTTP_INTERNAL_SERVER_ERROR)
  {
    $this->response->error($errorMessage, $statusCode);
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
   * @param  string $templatePath
   * @return void
   */
  protected function beforeResponseTemplate(string $templatePath) {}


  // ----------------------------------------------------------------
  // Deprecated
  /**
   * @deprecated Not recommended. It is obsolete in version 3.0.0 or later.
   */
  protected function response_json()
  {
    $this->responseJson();
  }

  /**
   * @deprecated Not recommended. It is obsolete in version 3.0.0 or later.
   */
  protected function set_status(int $status)
  {
    return $this->status($status);
  }

  /**
   * @deprecated Not recommended. It is obsolete in version 3.0.0 or later.
   */
  protected function response_html(string $htmlCode, string $char = 'utf-8')
  {
    $this->responseHtml($htmlCode, $char);
  }

  /**
   * @deprecated Not recommended. It is obsolete in version 3.0.0 or later.
   */
  protected function response_template(string $templatePath, string $char = 'utf-8')
  {
    $this->responseTemplate($templatePath, $char);
  }

  /**
   * @deprecated Not recommended. It is obsolete in version 3.0.0 or later.
   */
  protected function response_javascript(string $scriptCode, string $char = 'UTF-8')
  {
    $this->responseJavascript($scriptCode, $char);
  }

  /**
   * @deprecated Not recommended. It is obsolete in version 3.0.0 or later.
   */
  protected function response_text(string $text, string $char = 'UTF-8')
  {
    $this->responseText($text, $char);
  }

  /**
   * @deprecated Not recommended. It is obsolete in version 3.0.0 or later.
   */
  protected function response_download(string $fileMame, string $data = '', bool $mime = FALSE)
  {
    $this->responseDownload($fileMame, $data, $mime);
  }

  /**
   * @deprecated Not recommended. It is obsolete in version 3.0.0 or later.
   */
  protected function response_image(string $imagePath)
  {
    $this->responseImage($imagePath);
  }

  /**
   * @deprecated Not recommended. It is obsolete in version 3.0.0 or later.
   */
  protected function response_error(string $errorMessage, int $statusCode = \X\Constant\HTTP_INTERNAL_SERVER_ERROR)
  {
    $this->responseError($errorMessage, $statusCode);
  }

  /**
   * @deprecated Not recommended. It is obsolete in version 3.0.0 or later.
   */
  protected function before_response_json() {}

  /**
   * @deprecated Not recommended. It is obsolete in version 3.0.0 or later.
   */
  protected function before_response_template(string $templatePath) {}
}