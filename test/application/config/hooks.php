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
  $accessControl = AnnotationReader::getMethodAccessControl($ci->router->class, $ci->router->method);
  Logger::d('$ci->router->class=', $ci->router->class);
  Logger::d('$ci->router->method=', $ci->router->method);
  Logger::d('$accessControl=', $accessControl);
  $loggedin = !empty($_SESSION['user']);
  if ($loggedin && !$accessControl->allow_login_user) {
    // In case of an action that the logged-in user can not access
    redirect('/dashboard');
  } else if (!$loggedin && !$accessControl->allow_logoff_user) {
    // In case of an action that can not be accessed by the user who is logging off
    redirect('/login');
  }
};