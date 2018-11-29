<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use X\Util\Logger;
class PostControllerConstructor
{

  public function run()
  {
    $ci =& get_instance();
    $method = new \ReflectionMethod($ci->router->class, $ci->router->method);
    $isAllow = preg_match('/@allowLoggedIn/', $method->getDocComment()) === 1;
    Logger::d('$isAllow=', $isAllow ? 1111 : 0);
  }
}
