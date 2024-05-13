<?php
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
  $loggedin = !empty($_SESSION[SESSION_NAME]);
  $current = lcfirst($CI->router->directory ?? '') . lcfirst($CI->router->class) . '/' . $CI->router->method;
  $default = '/users/index';
  $allowRoles = !empty($meta->allow_role) ? array_map('trim', explode(',', $meta->allow_role)) : null;
  if (!$meta->allow_http)
    throw new \RuntimeException('HTTP access is not allowed');
  else if ($loggedin && !$meta->allow_login)
    redirect($default);
  else if (!$loggedin && !$meta->allow_logoff)
    redirect('/users/login');
  else if ($loggedin && !empty($allowRoles)) {
    $role = $_SESSION[SESSION_NAME]['role'] ?? '';
    if (!in_array($role, $allowRoles) && $default !== $current)
      redirect($default);
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