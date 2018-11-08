<?php
/**
 * Template util class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Util;
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