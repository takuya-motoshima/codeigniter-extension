<?php
require_once(dirname(__FILE__) . '/../core/AppException.php');

class UserNotFoundException extends AppException {
  public function __construct() {
    parent::__construct('User not found');
  }
}