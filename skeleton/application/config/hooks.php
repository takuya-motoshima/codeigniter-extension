<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/
use \X\Annotation\AnnotationReader;
use \X\Util\Logger;

$hook['post_controller_constructor'] = function() {
  $ci =& get_instance();
  $accessibility = AnnotationReader::getAccessibility($ci->router->class, $ci->router->method);
  $isLogin = !empty($_SESSION[SESSION_NAME]);
  if (!is_cli()) {
    if (!$accessibility->allow_http)
      throw new \RuntimeException('HTTP access is not allowed.');
    if ($isLogin && !$accessibility->allow_login)
      redirect('/userlist');
    else if (!$isLogin && !$accessibility->allow_logoff)
      redirect('/users/login');
  }
};

// $hook['pre_system'] = function () {
//   $dotenv = Dotenv\Dotenv::createImmutable(ENVIRONMENT_VARIABLE);
//   $dotenv->load();
//   set_exception_handler(function ($e) {
//     Logger::error($e);
//   });
// };