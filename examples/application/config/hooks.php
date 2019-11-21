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
  if ($_SESSION['user'] && (!$accessibility->allow_login || ($accessibility->allow_role && $accessibility->allow_role !== $_SESSION['user']['role']))) {
    // When the login user performs a non-access action.
    redirect('/dashboard');
  } else if (!$_SESSION['user'] && !$accessibility->allow_logoff) {
    // Logoff user performs a non-access action.
    redirect('/login');
  }
};