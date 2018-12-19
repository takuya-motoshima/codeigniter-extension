<?php
/**
 * Template util class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Util;
use X\Util\Logger;
final class Template
{
  /**
   * @var Twig_Environment
   */
  private $engine = null;

  /**
   * 
   * construct
   *
   * @param array $option
   */
  public function __construct(array $option = [])
  {
    $option = array_merge([
      'paths' => [
        \VIEWPATH,
        realpath(__DIR__ . '/../') . '/Template',
      ],
      'environment' => [
        // 'cache' => false,
        'cache' => \APPPATH . 'cache',
        'debug' => \ENVIRONMENT !== 'production',
        'autoescape' => 'html',
      ],
      'lexer' => [
        'tag_comment' => ['{#','#}'],
        'tag_block' => ['{%','%}'],
        'tag_variable' => ['{{','}}'],
        'interpolation' => ['#{','}'],
      ],
    ], $option);
    $this->engine = new \Twig_Environment(new \Twig_Loader_Filesystem($option['paths']), $option['environment']);
    $this->engine->addFunction(new \Twig_SimpleFunction('cache_busting',
      /**
       * @param $filePath
       * This function generates a new file path with the last date of filechange
       * to support better better client caching via Expires header:
       * i.e:
       * css/style.css -> css/style.css?1428423235
       * // css/style.css -> css/style.1428423235.css
       *
       * Usage in template files:
       * 
       * i.e:
       * <link rel="stylesheet" href="{{ cache_busting('css/style.css') }}">
       *
       * Apache Rewrite Rule:
       *
       * RewriteCond %{REQUEST_FILENAME} !-f
       * RewriteCond %{REQUEST_FILENAME} !-d
       * RewriteRule ^(.*)\.[\d]{10}\.(css|js)$ $1.$2 [NC,L]
       *
       * Apache Document Root MUST be configured without the trailing slash!
       *
       * @return mixed
       */
      function (string $filePath) {
        $modified = filemtime($_SERVER['DOCUMENT_ROOT'] . '/' . $filePath);
        if (!$modified) {
          //Fallback if mtime could not be found:
          $modified = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        }
        return \base_url($filePath) . '?' . $modified;
        // return preg_replace('{\\.([^./]+)$}', ".$modified.\$1", $filePath);
      }
    ));
    $baseUrl = \base_url();
    $this->engine->addGlobal('base_url', $baseUrl);
    $this->engine->addGlobal('session', $_SESSION ?? null);
    $ci =& get_instance();
    $this->engine->addGlobal('action', ($ci->router->directory ?? '') . $ci->router->class . '/' . $ci->router->method);
    $this->engine->setLexer(new \Twig_Lexer($this->engine, $option['lexer']));
  }

  /**
   * Load template
   *
   * @param  string $path
   * @param  array  $vars
   * @return string
   */
  public function load(string $path, array $vars = [], string $extension = 'html'):string
  {
    return $this->engine->render($path . '.' . $extension, $vars);
  }
}