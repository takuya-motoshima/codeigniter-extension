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
  $islogin = !empty($_SESSION[SESSION_NAME]);

  // Whether it is HTTP access.
  $ishttp = !is_cli();

  // When accessed by HTTP.
  if ($ishttp) {
    // Returns an error if HTTP access is not allowed.
    if (!$accessibility->allow_http) throw new \RuntimeException('HTTP access is not allowed.');

    // When the logged-in user calls a request that only the log-off user can access, redirect to the dashboard.
    // It also redirects to the login page when the log-off user calls a request that only the logged-in user can access.
    if ($islogin && !$accessibility->allow_login) redirect('/dashboard');
    else if (!$islogin && !$accessibility->allow_logoff) redirect('/login');
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
  });
};