<?php
/**
 * HTTP Response util class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Util;
use X\Constant\HttpStatus;
use X\Util\Loader;
final class HttpResponse {

  /**
   * @var array $data
   */
  private $data = [];

  /**
   * @var int $status
   */
  private $status;

  /**
   * @var CI_Controller $ci
   */
  private $ci;

  /**
   * Construct
   */
  public function __construct() {
    $this->ci =& \get_instance();
  }

  /**
   * Set data
   *
   * @param  mixed $key
   * @param  mixed $value
   * @return object
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
   * Clear data
   *
   * @return object
   */
  public function clear() {
    $this->data = [];
    return $this;
  }

  /**
   * Set status
   *
   * @param  int $status
   * @return object
   */
  public function status(int $status) {
    $this->status = $status;
    return $this;
  }

  /**
   * Response JSON
   *
   * @throws LogicException
   * @param  bool $forceObject
   * @param  bool $prettyrint
   * @return void
   */
 public function json(bool $forceObject = false, bool $prettyrint = false) {
    $options = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
    if ($forceObject) {
      $options = $options | JSON_FORCE_OBJECT;
    }
    if ($prettyrint) {
      $options = $options | JSON_PRETTY_PRINT;
    }
    $json = json_encode($this->data, $options);
    if ($json === false) {
      throw new \LogicException(sprintf('Failed to parse json string \'%s\', error: \'%s\'', $this->data, json_last_error_msg()));
    }
    ob_clean();
    $this->header();
    $this->ci->output
      ->set_status_header($this->status ?? \X\Constant\HTTP_OK)
      ->set_content_type('application/json', 'UTF-8')
      ->set_output($json);
  }

  /**
   * Response HTML
   *
   * @param  string  $html
   * @return void
   */
  public function html(string $html) {
    $this->header();
    $this->ci->output
      ->set_content_type('text/html', 'UTF-8')
      ->set_output($html);
  }

  /**
   * Response template
   *
   * @param  string $path
   * @return void
   */
  public function view(string $path) {
    static $template;
    $template = $template ?? new \X\Util\Template();
    self::html($template->load($path, $this->data));
  }

  /**
   * Response js
   *
   * @param  string $js
   * @return void
   */
  public function js(string $js) {
    ob_clean();
    $this->header();
    $this->ci->output
      ->set_content_type('application/javascript', 'UTF-8')
      ->set_output($js);
  }

  /**
   * Response text
   *
   * @param  string $text
   * @return void
   */
  public function text(string $text) {
    ob_clean();
    $this->header();
    $this->ci->output
      ->set_content_type('text/plain', 'UTF-8')
      ->set_output($text);
  }

  /**
   * Response download
   *
   * @param  string $file
   * @param  string $data
   * @param  bool $mime
   * @return void
   */
  public function download(string $file, string $data = '', bool $mime = FALSE) {
    ob_clean();
    $this->ci->load->helper('download');
    force_download($file, $data, $mime);
  }

  /**
   * Response image
   *
   * @param  string $path
   * @return void
   */
  public function image(string $path) {
    ob_clean();
    $this->ci->load->helper('file');
    $this->ci->output
      ->set_content_type(get_mime_by_extension($path))
      ->set_output(file_get_contents($path));
  }

  /**
   * Response error
   *
   * @param  string $message
   * @param  int $status
   * @return void
   */
  public function error(string $message, int $status = \X\Constant\HTTP_INTERNAL_SERVER_ERROR) {
    if ($this->ci->input->is_ajax_request()) {
      ob_clean();
      $this->header();
      $this->ci->output
        ->set_header('Cache-Control: no-cache, must-revalidate')
        ->set_status_header($status, rawurlencode($message))
        ->set_content_type('application/json', 'UTF-8');
    } else {
      show_error($message, $status);
    }
  }

  /**
   * Set header
   *
   * @param
   */
  private function header() {
    $origin = '*';
    if (!empty($_SERVER['HTTP_ORIGIN'])) {
      $origin = $_SERVER['HTTP_ORIGIN'];
    } else if (!empty($_SERVER['HTTP_REFERER'])) {
      $origin = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_SCHEME) . '://' . parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
    }
    $this->ci->output
      ->set_header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization')
      ->set_header('Access-Control-Allow-Methods: GET, POST, OPTIONS')
      ->set_header('Access-Control-Allow-Credentials: true')
      ->set_header('Access-Control-Allow-Origin: ' . $origin);
  }
}