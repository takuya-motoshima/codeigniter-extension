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

// post_controller_constructor callback.
$hook['post_controller_constructor'] = function() {
  $ci =& get_instance();

  // Get access from annotations.
  $accessibility = AnnotationReader::getAccessibility($ci->router->class, $ci->router->method);

  // Whether you are logged in.
  $isLogin = !empty($_SESSION[SESSION_NAME]);

  // Whether it is HTTP access.
  $isHttp = !is_cli();

  // Requested path.
  $curPath = lcfirst($ci->router->directory ?? '') . lcfirst($ci->router->class) . '/' . $ci->router->method;

  // Default path.
  $defPath = '/dashboard';

  // Roles that allow access.
  $allowRoles = !empty($accessibility->allow_role) ? array_map('trim', explode(',', $accessibility->allow_role)) : null;

  if ($isHttp) {
    // When accessed by HTTP.
    if (!$accessibility->allow_http)
      throw new \RuntimeException('HTTP access is not allowed');
    else if ($isLogin && !$accessibility->allow_login)
      redirect($defPath);
    else if (!$isLogin && !$accessibility->allow_logoff)
      redirect('/login');
    else if ($isLogin && !empty($allowRoles)) {
      $role = $_SESSION[SESSION_NAME]['role'] ?? 'undefined';
      if (!in_array($role, $allowRoles) && $defPath !== $curPath)
        redirect($defPath);
    }
  } else {
    // When executed with CLI.
  }
};

// pre_system callback.
$hook['pre_system'] = function () {
  // Load environment variables.
  $dotenv = Dotenv\Dotenv::createImmutable(ENV_DIR);
  $dotenv->load();

  // Check for uncaught exceptions.
  set_exception_handler(function ($e) {
    Logger::error($e);
    show_error('This page is not working', 500);
  });
};