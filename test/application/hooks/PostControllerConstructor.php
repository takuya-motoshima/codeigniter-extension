<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Util\Logger;
use \X\Annotation\AnnotationReader;
class PostControllerConstructor
{

  public function run()
  {
    $ci =& get_instance();
    AnnotationReader::accessControl($ci->router->class, $ci->router->method);
  }
}
