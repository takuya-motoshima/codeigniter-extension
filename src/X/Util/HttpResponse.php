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
final class HttpResponse
{

    /**
     * @var array $data
     */
    private $data = [];

    /**
     * @var int $status
     */
    private $status;

    /**
     * 
     * Response JSON
     *
     * @throws LogicException
     * @param  bool $forceObject
     * @param  bool $pretty
     * @param  bool $unescapedSlashes
     * @return void
     */
    public function json(
        bool $forceObject = false, 
        bool $pretty = false,
        bool $unescapedSlashes = true
    )
    {
        $option = 0;
        $forceObject && $option = $option | JSON_FORCE_OBJECT;
        $pretty && $option = $option | JSON_PRETTY_PRINT;
        $unescapedSlashes && $option = $option | JSON_UNESCAPED_SLASHES;
        $json = json_encode($this->data, $option);
        if ($json === false) {
            throw new \LogicException(sprintf('Failed to parse json string \'%s\', error: \'%s\'', $this->data, json_last_error_msg()));
        }
        ob_clean();
        $ci =& \get_instance();
        $this->set_cors_header($ci);
        $ci->output
            ->set_status_header($this->status ?? \X\Constant\HTTP_OK)
            ->set_content_type('application/json', 'UTF-8')
            ->set_output($json);
    }

    /**
     * 
     * Response HTML
     *
     * @param  string  $html
     * @param  string $char
     * @return void
     */
    public function html(string $html, string $char = 'UTF-8')
    {
        $ci =& \get_instance();
        $this->set_cors_header($ci);
        $ci->output
            ->set_content_type('text/html', $char)
            ->set_output($html);
    }

    /**
     * 
     * Response HTML for template
     *
     * @param  string $teamplatePath
     * @param  string $char
     * @return void
     */
    public function template(string $templatePath, string $char = 'UTF-8')
    {
        static $template;
        $template = $template ?? new \X\Util\Template();
        self::html($template->load($templatePath, $this->data));
    }

    /**
     * 
     * Response javascript
     *
     * @param  string $code
     * @param  string $char
     * @return void
     */
    public function javascript(string $code, string $char = 'UTF-8')
    {
        ob_clean();
        $ci =& \get_instance();
        $this->set_cors_header($ci);
        $ci->output
            ->set_content_type('application/javascript', $char)
            ->set_output($code);
    }

    /**
     * 
     * Response download
     *
     * @param  string $filename
     * @param  string $data
     * @param  bool $set_mime
     * @return void
     */
    public function download(string $filename, string $data = '', bool $set_mime = FALSE)
    {
        $ci =& \get_instance();
        $ci->load->helper('download');
        ob_clean();
        force_download($filename, $data, $set_mime);
    }

    /**
     * 
     * Response image
     *
     * @param  string $image_path
     * @return void
     */
    public function image(string $image_path)
    {
        $ci =& \get_instance();
        $ci->load->helper('file');
        ob_clean();
        $ci->output
            ->set_content_type(get_mime_by_extension($image_path))
            ->set_output(file_get_contents($image_path));
    }

    /**
     * 
     * Response error
     *
     * @param  string $errorMessage
     * @param  int $httStatus
     * @return void
     */
    public function error(string $errorMessage, int $httStatus = \X\Constant\HTTP_INTERNAL_SERVER_ERROR)
    {
        $ci =& \get_instance();
        if ($ci->input->is_ajax_request()) {
            ob_clean();
            $this->set_cors_header($ci);
            $ci->output
                ->set_header('Cache-Control: no-cache, must-revalidate')
                ->set_status_header($httStatus, rawurlencode($errorMessage))
                ->set_content_type('application/json', 'UTF-8');
        } else {
            show_error($errorMessage, $httStatus);
        }
    }

    /**
     * 
     * Set http status
     *
     * @param  int $status
     * @return object
     */
    public function set_status(int $status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * 
     * Set response data
     *
     * @param  mixed $key
     * @param  mixed $value
     * @return object
     */
    public function set($key, $value = null)
    {
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
     * 
     * Clear response data
     *
     * @return object
     */
    public function clear()
    {
        $this->data[] = [];
        return $this;
    }

    /**
     * Set CORS header
     *
     * @param
     */
    public function set_cors_header(\CI_Controller &$ci)
    {
        $http_origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
        $ci->output
            ->set_header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization')
            ->set_header('Access-Control-Allow-Methods: GET, POST, OPTIONS')
            ->set_header('Access-Control-Allow-Credentials: true')
            ->set_header('Access-Control-Allow-Origin: ' . $http_origin);
    }
}