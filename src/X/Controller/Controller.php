<?php
namespace X\Controller;
use \X\Util\HttpResponse;
use \X\Util\Loader;

/**
 * CI_Controller extension.
 */
#[\AllowDynamicProperties]
abstract class Controller extends \CI_Controller {
  /**
   * Auto-loading model name.
   * @var string|string[]
   */
  protected $model;

  /**
   * Auto-loading library name.
   * @var string|string[]
   */
  protected $library;

  /**
   * HttpResponse instance.
   * @var HttpResponse
   */
  protected $httpResponse;

  /**
   * Initialize the controller.
   */
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
   * // Allow all.
   * parent::setCorsHeader('*');
   *
   * // Only any origin is allowed.
   * parent::setCorsHeader('http://www.example.jp');
   * parent::setCorsHeader('http://www.example.jp https://www.example.jp http://sub.example.jp');
   *
   * // To set the same Access-Control-Allow-Origin for all responses, use the hook point called before the response.
   * abstract class AppController extends \X\Controller\Controller {
   *   protected function beforeResponse(string $referer) {
   *     $this->setCorsHeader('*');
   *   }
   * }
   * ```
   * @param string $origin Allowable Origins.
   * @return Controller
   */
  protected function setCorsHeader(string $origin='*') {
    $this->httpResponse->setCorsHeader($origin);
    return $this;
  }

  /**
   * Set response.
   * @param mixed $key If one argument, the response data. If two arguments, the field name of the response data.
   * @param mixed|null $value Response data.
   * @return Controller
   */
  protected function set($key, $value=null) {
    func_num_args() === 1
      ? $this->httpResponse->set($key)
      : $this->httpResponse->set($key, $value);
    return $this;
  }

  /**
   * Clear response.
   * @return Controller
   */
  protected function clear() {
    $this->httpResponse->clear($key, $value);
    return $this;
  }

  /**
   * Set HTTP status.
   * @param int $httpStatus HTTP status.
   * @return Controller
   */
  protected function status(int $httpStatus) {
    $this->httpResponse->status($httpStatus);
    return $this;
  }

  /**
   * Response JSON.
   * @param bool $forceObject (optional) Outputs an object rather than an array when a non-associative array is used.
   * @param bool $prettyrint (optional) Use whitespace in returned data to format it.
   * @return void
   */
  protected function json(bool $forceObject=false, bool $prettyrint=false): void {
    $this->beforeResponse($this->getReferer());
    $this->beforeResponseJson($this->getReferer());
    $this->httpResponse->json($forceObject, $prettyrint);
  }

  /**
   * Response HTML.
   * @param string $html HTML string.
   * @return void
   */
  protected function html(string $html): void {
    $this->beforeResponse($this->getReferer());
    $this->beforeResponseHtml($this->getReferer());
    $this->httpResponse->html($html);
  }

  /**
   * Responds with the result of compiling the specified template into HTML.
   * @param string $templatePath Template path.
   * @return void
   */
  protected function view(string $templatePath): void {
    $this->beforeResponse($this->getReferer());
    $this->beforeResponseView($this->getReferer());
    $this->httpResponse->view($templatePath);
  }

  /**
   * Response js.
   * @param string $js JS Code.
   * @return void
   */
  protected function js(string $js): void {
    $this->beforeResponse($this->getReferer());
    $this->beforeResponseJs($this->getReferer());
    $this->httpResponse->js($js);
  }

  /**
   * Response Plain text.
   * @param string $plainText Plain text.
   * @return void
   */
  protected function text(string $plainText):void {
    $this->beforeResponse($this->getReferer());
    $this->beforeResponseText($this->getReferer());
    $this->httpResponse->text($plainText);
  }

  /**
   * Download file.
   * @param string $filename Download file name.
   * @param string $content (optional) Downloadable Content.
   * @param bool $mime (optional) MIME Type. The default is false and the MIME type is automatically detected.
   * @return void
   */
  protected function download(string $filename, string $content='', bool $mime=false): void {
    $this->beforeResponse($this->getReferer());
    $this->beforeDownload($this->getReferer());
    $this->httpResponse->download($filename, $content, $mime);
  }

  /**
   * Response image.
   * @param string $imagePath Image path.
   * @return void
   */
  protected function image(string $imagePath): void {
    $this->beforeResponse($this->getReferer());
    $this->beforeResponseImage($this->getReferer());
    $this->httpResponse->image($imagePath);
  }

  /**
   * Internal redirect.
   * Allows for internal redirection to a location determined by a header returned from a backend.
   * This allows the backend to authenticate and perform any other processing,
   * provide content to the end user from the internally redirected location,
   * and free up the backend to handle other requests.
   *
   * Nginx:
   * ```nginx
   * # Will serve /var/www/files/myfile
   * # When passed URI /protected/myfile
   * location /protected {
   *   internal;
   *   alias /var/www/files;
   * }
   * ``
   * 
   * PHP:
   * ```php
   * class Sample extends \X\Controller\Controller {
   *   public function index() {
   *     parent::internalRedirect('/protected/myfile');
   *   }
   * }
   * ```
   * @param string $redirectPath Path to internal redirect.
   * @return void
   */
  public function internalRedirect(string $redirectPath): void {
    $this->beforeResponse($this->getReferer());
    $this->beforeInternalRedirect($this->getReferer());
    $this->httpResponse->internalRedirect($redirectPath);
  }

  /**
   * Error response.
   * @param string $message Error message.
   * @param int $httpStatus (optional) HTTP status.
   * @param bool $forceJsonResponse (optional) Force a response with Content-Type "application/json".
   * @return void
   */
  protected function error(string $message, int $httpStatus=500, bool $forceJsonResponse=false): void {
    $this->httpResponse->error($message, $httpStatus, $forceJsonResponse);
  }

  /**
   * Get referrer.
   * @return string Referrer.
   */
  private function getReferer(): string {
    if (!empty($_SERVER['HTTP_REFERER']))
      return $_SERVER['HTTP_REFERER'];
    $protocol = 'http';
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
      $protocol = 'https';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  }

  /**
   * Called just before response. Override as needed.
   * @param string $referer Referrer.
   */
  protected function beforeResponse(string $referer) {}

  /**
   * Called just before the JSON response. Override as needed.
   * @param string $referer Referrer.
   */
  protected function beforeResponseJson(string $referer) {}

  /**
   * Called just before the template response. Override as needed.
   * @param string $referer Referrer.
   */
  protected function beforeResponseView(string $referer) {}

  /**
   * Called just before the HTML response. Override as needed.
   * @param string $referer Referrer.
   */
  protected function beforeResponseHtml(string $referer) {}

  /**
   * Called just before the JS response. Override as needed.
   * @param string $referer Referrer.
   */
  protected function beforeResponseJs(string $referer) {}

  /**
   * Called just before plain text response. Override as needed.
   * @param string $referer Referrer.
   */
  protected function beforeResponseText(string $referer) {}

  /**
   * Called just before downloading. Override as needed.
   * @param string $referer Referrer.
   */
  protected function beforeDownload(string $referer) {}

  /**
   * Called just before the image response. Override as needed.
   * @param string $referer Referrer.
   */
  protected function beforeResponseImage(string $referer) {}

  /**
   * Called just before the internal redirect. Override as needed.
   * @param string $referer Referrer.
   */
  protected function beforeInternalRedirect(string $referer) {}
}