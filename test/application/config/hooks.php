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
$hook['post_controller_constructor'] = function() {
  $ci =& get_instance();
  $accessControl = AnnotationReader::getMethodAccessControl($ci->router->class, $ci->router->method);
  $loggedin = !empty($_SESSION['user']);
  if ($loggedin && !$accessControl->allowLoggedin) {
    // In case of an action that the logged-in user can not access
    redirect('/dashboard');
  } else if (!$loggedin && !$accessControl->allowLoggedoff) {
    // In case of an action that can not be accessed by the user who is logging off
    redirect('/login');
  }
};