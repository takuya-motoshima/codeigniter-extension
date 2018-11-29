<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Util\Logger;
use \X\Security\AnnotationAuthentication;
class PostControllerConstructor
{

  public function run()
  {
    $ci =& get_instance();
    $method = new \ReflectionMethod($ci->router->class, $ci->router->method);
    $isAccessible = AnnotationAuthentication::isAccessible($ci->router->class, $ci->router->method, true);
    Logger::d('$isAccessible=', $isAccessible ? 1 : 0);
  }
}
