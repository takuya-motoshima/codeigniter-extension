<?php
namespace X\Util;
use X\Constant\HttpStatus;
use X\Util\Loader;

final class HttpResponse {
  private $data = [];
  private $status;
  private $ci;

  public function __construct() {
    $this->ci =& \get_instance();
  }

  /**
   * Set data.
   */
  public function set($key, $value = null) {
    if (func_num_args() === 2) {
      if (!is_array($this->data)) {
        $this->data = [];
      }
      $this->data[$key] = $value;
    } else if (func_num_args() === 1) {
      $this->data = $key;
    }
    return $this;
  }

  /**
   * Clear data.
   */
  public function clear() {
    $this->data = [];
    return $this;
  }

  /**
   * Set status.
   */
  public function status(int $status) {
    $this->status = $status;
    return $this;
  }

  /**
   * Response JSON.
   */
 public function json(bool $forceObject = false, bool $prettyrint = false) {
    $options = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
    if ($forceObject)
      $options = $options | JSON_FORCE_OBJECT;
    if ($prettyrint)
      $options = $options | JSON_PRETTY_PRINT;
    $json = json_encode($this->data, $options);
    if ($json === false)
      throw new \LogicException(sprintf('Failed to parse json string \'%s\', error: \'%s\'', $this->data, json_last_error_msg()));
    ob_clean();
    // $this->setCorsHeader('*');
    $this->ci->output
      ->set_status_header($this->status ?? \X\Constant\HTTP_OK)
      ->set_content_type('application/json', 'UTF-8')
      ->set_output($json);
  }

  /**
   * Response HTML.
   */
  public function html(string $html) {
    // $this->setCorsHeader('*');
    $this->ci->output
      ->set_content_type('text/html', 'UTF-8')
      ->set_output($html);
  }

  /**
   * Response template.
   */
  public function view(string $path) {
    static $template;
    $template = $template ?? new \X\Util\Template();
    self::html($template->load($path, $this->data));
  }

  /**
   * Response js.
   */
  public function js(string $js) {
    ob_clean();
    // $this->setCorsHeader('*');
    $this->ci->output
      ->set_content_type('application/javascript', 'UTF-8')
      ->set_output($js);
  }

  /**
   * Response text.
   */
  public function text(string $text) {
    ob_clean();
    // $this->setCorsHeader('*');
    $this->ci->output
      ->set_content_type('text/plain', 'UTF-8')
      ->set_output($text);
  }

  /**
   * Response download.
   */
  public function download(string $file, string $data = '', bool $mime = FALSE) {
    ob_clean();
    $this->ci->load->helper('download');
    force_download($file, $data, $mime);
  }

  /**
   * Response image.
   */
  public function image(string $path) {
    ob_clean();
    $this->ci->load->helper('file');
    $this->ci->output
      ->set_content_type(get_mime_by_extension($path))
      ->set_output(file_get_contents($path));
  }

  /**
   * Response error.
   */
  public function error(string $message, int $status = \X\Constant\HTTP_INTERNAL_SERVER_ERROR) {
    if ($this->ci->input->is_ajax_request()) {
      ob_clean();
      // $this->setCorsHeader('*');
      $this->ci->output
        ->set_header('Cache-Control: no-cache, must-revalidate')
        ->set_status_header($status, rawurlencode($message))
        ->set_content_type('application/json', 'UTF-8');
    } else {
      show_error($message, $status);
    }
  }

  /**
   * Internal redirect.
   * Allows for internal redirection to a location determined by a header returned from a backend.
   * This allows the backend to authenticate and perform any other processing,
   * provide content to the end user from the internally redirected location,
   * and free up the backend to handle other requests.
   *
   * Nginx configuration example: 
   * # Will serve /var/www/files/myfile
   * # When passed URI /protected_files/myfile
   * location /protected_files {
   *   internal;
   *   alias /var/www/files;
   * }
   * 
   * Codeigniter controller example.
   * <code>
   * <?php
   * class Files extends \X\Controller\Controller {
   *   public function index(string $fileName) {
   *     parent::internalRedirect('/protected_files/myfile');
   *   }
   * }
   * </code>
   */
  public function internalRedirect(string $internalRedirectPath) {
    // $this->setCorsHeader('*');
    $this->ci->output
      ->set_header('Content-Type: true')
      ->set_header('X-Accel-Redirect: ' . $internalRedirectPath)
      ->set_status_header(\X\Constant\HTTP_OK);
  }

  /**
   * Sets the CORS header.
   *
   * <code>
   * <?php
   * // Allow all origins
   * $httpResponse->setCorsHeader('*');
   *
   * // Allow a specific single origin
   * $httpResponse->setCorsHeader('http://www.example.jp');
   * 
   * // Allow specific multiple origins
   * $httpResponse->setCorsHeader('http://www.example.jp https://www.example.jp http://sub.example.jp');
   */
  public function setCorsHeader(string $origin) {
    if ($origin === '*') {
      if (!empty($_SERVER['HTTP_ORIGIN']))
        $origin = $_SERVER['HTTP_ORIGIN'];
      else if (!empty($_SERVER['HTTP_REFERER']))
        $origin = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_SCHEME) . '://' . parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
    }
    $this->ci->output
      ->set_header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization')
      ->set_header('Access-Control-Allow-Methods: GET, POST, OPTIONS')
      ->set_header('Access-Control-Allow-Credentials: true')
      ->set_header('Access-Control-Allow-Origin: ' . $origin);
  }
}