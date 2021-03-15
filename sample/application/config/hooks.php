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
  isset($_SESSION['user']) ? handlingLoggedIn() : handlingLogOff();
};

// pre_system callback.
$hook['pre_system'] = function () {
  // Load environment variables.
  $dotenv = Dotenv\Dotenv::createImmutable(realpath(APPPATH . '..'));
  $dotenv->load();

  // set_error_handler(function ($err_level, $err_msg, $err_file, $err_line, $err_context) {
  //   Logger::print('set_error_handler');
  // }, E_WARNING | E_NOTICE);

  // Check for uncaught exceptions.
  set_exception_handler(function ($e) {
    Logger::error($e);
  });
};

/**
 * Process for logged-in user
 */
function handlingLoggedIn() {
  $ci =& get_instance();

  // If it is BAN, call the logoff process
  $ci->load->model('UserService');
  if ($ci->UserService->isBanUser(session_id())) {
    // Sign out
    $ci->UserService->signout();
    // Set ban message display flag
    $ci->load->helper('cookie');
    set_cookie('show_ban_message', true, 10);
    // To logoff processing
    return handlingLogOff();
  }

  // Check if the request URL has access privileges
  $accessibility = AnnotationReader::getAccessibility($ci->router->class, $ci->router->method);
  if (!$accessibility->allow_login || ($accessibility->allow_role && $accessibility->allow_role !== $session['role'])) {
    // In case of access prohibition action, redirect to the dashboard page
    redirect('/dashboard');
  }
}

/**
 * Process for logoff user
 */
function handlingLogOff() {
  $ci =& get_instance();

  // Check if the request URL has access privileges
  $accessibility = AnnotationReader::getAccessibility($ci->router->class, $ci->router->method);
  if (!$accessibility->allow_logoff) {
    // In case of access prohibition action, redirect to the login page
    redirect('/signin');
  }
}

