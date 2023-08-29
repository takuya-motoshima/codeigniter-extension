<?php
namespace X\Util;
use X\Util\FileHelper;
use X\Util\Loader;
use X\Util\Logger;

final class Template {
  private $engine = null;

  /**
   * Construct.
   */
  public function __construct(array $option = []) {
    $cache = Loader::config('config', 'cache_templates');
    if (!empty($cache)) {
      FileHelper::makeDirectory($cache);
    }
    $option = array_merge([
      'paths' => [ \VIEWPATH ],
      'environment' => [
        'cache' => !empty($cache) ? $cache : false,
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
       * This function generates a new file path with the last date of filechange to support better better client caching via Expires header:
       * e.g. <link rel="stylesheet" href="{{cache_busting('css/style.css')}}">
       *       css/style.css -> css/style.css?1428423235
       */
      function (string $filePath) {
        if (!file_exists(FCPATH . $filePath))
          return \base_url($filePath);
        $modified = filemtime($_SERVER['DOCUMENT_ROOT'] . '/' . $filePath);
        if (!$modified)
          $modified = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        return \base_url($filePath) . '?' . $modified;
        // return preg_replace('{\\.([^./]+)$}', ".$modified.\$1", $filePath);
      }
    ));
    $baseUrl = \base_url();
    $this->engine->addGlobal('baseUrl', $baseUrl);
    $this->engine->addGlobal('session', $_SESSION ?? null);
    $CI =& get_instance();
    $this->engine->addGlobal('action', ($CI->router->directory ?? '') . $CI->router->class . '/' . $CI->router->method);
    $this->engine->setLexer(new \Twig_Lexer($this->engine, $option['lexer']));
  }

  /**
   * Load template engine.
   */
  public function load(string $path, array $vars = [], string $extension = 'html'): string {
    return $this->engine->render($path . '.' . $extension, $vars);
  }
}