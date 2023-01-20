<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/userguide3/general/hooks.html
|
*/
use \X\Annotation\AnnotationReader;
use \X\Util\Logger;

$hook['post_controller_constructor'] = function() {
  if (is_cli())
    return;
  $CI =& get_instance();
  $meta = AnnotationReader::getAccessibility($CI->router->class, $CI->router->method);
  $isLogin = !empty($_SESSION[SESSION_NAME]);
  $currentPath = lcfirst($CI->router->directory ?? '') . lcfirst($CI->router->class) . '/' . $CI->router->method;
  $defaultPath = '/users/index';
  $allowRoles = !empty($meta->allow_role) ? array_map('trim', explode(',', $meta->allow_role)) : null;
  if (!$meta->allow_http)
    throw new \RuntimeException('HTTP access is not allowed');
  else if ($isLogin && !$meta->allow_login)
    redirect($defaultPath);
  else if (!$isLogin && !$meta->allow_logoff)
    redirect('/users/login');
  else if ($isLogin && !empty($allowRoles)) {
    $role = $_SESSION[SESSION_NAME]['role'] ?? '';
    if (!in_array($role, $allowRoles) && $defaultPath !== $currentPath)
      redirect($defaultPath);
  }
};

$hook['pre_system'] = function () {
  $dotenv = Dotenv\Dotenv::createImmutable(ENV_DIR);
  $dotenv->load();
  set_exception_handler(function ($e) {
    Logger::error($e);
    show_error($e->getMessage(), 500);
  });
};