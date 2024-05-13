<?php
namespace X\Util;
use \X\Util\FileHelper;
use \X\Util\Loader;
use \X\Util\Logger;

/**
 * Twig-based template.
 */
final class Template {
  /**
   * Twig_Environment instance.
   * @var \Twig_Environment
   */
  private $engine = null;

  /**
   * Initialize Template.
   * @param string[] $options[paths] Path of the directory where the template is located.
   * @param string|false $options[environment][cache] (optional) Absolute path to save the compiled template. Default is the value of `cache_templates` in `application/config/config.php`.
   * @param bool $options[environment][debug] (optional) When set to true, the generated templates have a __toString() method that you can use to display the generated nodes. Default is true if the `ENVIRONMENTP` environment variable is other than 'production'.
   * @param string|false $options[environment][autoescape] (optional) Sets the default auto-escaping strategy (name, html, js, css, url, html_attr, or a PHP callback that takes the template "filename" and returns the escaping strategy to use). set it to false to disable auto-escaping. The default is "html".
   * @param array $options[lexer][tag_comment] (optional) Comment Block. Default is `['{#','#}']`.
   * @param array $options[lexer][tag_block] Code block. Default is `['{%','%}']`.
   * @param array $options[lexer][tag_variable] Variable block. Default is `['{{','}}']`.
   * @param array $options[lexer][interpolation] String interpolation block. Interpolation (#{expression}) allows you to insert a valid expression within a string enclosed in double quotes. Default is `['#{','}']`.
   */
  public function __construct(array $options=[]) {
    $cache = Loader::config('config', 'cache_templates');
    if (!empty($cache))
      FileHelper::makeDirectory($cache);
    $options = array_merge([
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
    ], $options);
    $this->engine = new \Twig_Environment(new \Twig_Loader_Filesystem($options['paths']), $options['environment']);
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
    $this->engine->setLexer(new \Twig_Lexer($this->engine, $options['lexer']));
  }

  /**
   * Get compiled template.
   * @param string $templatePath Template Path.
   * @param array $params (optional) Template Variables.
   * @param string $ext (optional) Template file extension. Default is "html".
   * @return string Compiled template.
   */
  public function load(string $templatePath, array $params=[], string $ext='html'): string {
    return $this->engine->render($templatePath . '.' . $ext, $params);
  }
}