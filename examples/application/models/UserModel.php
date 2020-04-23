<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Util\Logger;

class UserModel extends \AppModel {

  const TABLE = 'user';

  public function signin(string $username, string $password): bool {
    // Logger::debug('$username=', $username);
    // Logger::debug('$password=', $password);
    $user = parent
      ::where('username', $username)
      ::where('password', $password)
      ::from(self::TABLE)
      ::get()
      ->row_array();
    if (empty($user)) {
      return false;
    }
    unset($user['password']);
    $_SESSION['user'] = $user;
    return true;
  }

  public function signout(): bool {
    unset($_SESSION['user']);
    return true;
  }

  public function getUserById(int $id): ?array {
    return parent
      ::select('id,role,username,created,modified')
      ::from(self::TABLE)
      ::where('id', $id)
      ::get()
      ->row_array();
  }

  public function addUser(array $set): int {
    return parent
      ::set($set)
      ::insert();
  }

  public function updateUser(int $id, array $set) {
    parent
      ::set($set)
      ::where('id', $set['id'])
      ::update();
  }

  public function deleteUser(int $id) {
    parent
      ::where('id', $set['id'])
      ::delete();
  }
}