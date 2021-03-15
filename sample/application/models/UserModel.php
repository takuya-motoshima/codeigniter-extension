<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Util\Logger;

class UserModel extends \AppModel {

  const TABLE = 'user';

  public function getUserByUsernameAndPassword(string $username, string $password): ?array {
    return parent
      ::where('username', $username)
      ::where('password', $password)
      ::from(self::TABLE)
      ::get()
      ->row_array();
  }

  public function getUserById(int $id): ?array {
    return parent
      ::select('id,role,username,created,modified')
      ::from(self::TABLE)
      ::where('id', $id)
      ::get()
      ->row_array();
  }
}