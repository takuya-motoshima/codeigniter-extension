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

  $controller = $ci->router->class;
  $action = $ci->router->method;
  $session = $_SESSION['user'] ?? null;

  // Logger::debug("\$controller=$controller");
  // Logger::debug("\$action=$action");

  $accessibility = AnnotationReader::getAccessibility($controller, $action);
  if (isset($session) && ( !$accessibility->allow_login || ($accessibility->allow_role && $accessibility->allow_role !== $session['role']) )) {
    redirect('/dashboard');
  } else if (!isset($session) && !$accessibility->allow_logoff) {
    redirect('/signin');
  }
};