<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Util\Logger;

class UserService extends \AppModel {

  protected $model = [
    'UserModel',
    'SessionModel'
  ];

  public function signin(string $username, string $password): bool {

    // Logger::debug('$username=', $username);
    // Logger::debug('$password=', $password);

    // Find data matching ID and password
    $user = $this->UserModel->getUserByUsernameAndPassword($username, $password);
    if (empty($user)) {
      return false;
    }
    unset($user['password']);

    // Change the BAN flag of other logged-in users to on
    $this->SessionModel->updateSessionBanFlagOn($username, session_id());
    // // Delete sessions with the same user ID and session IDs other than yourself
    // $this->SessionModel->deleteSessionOtherThanYourself($username, session_id());

    // Store login user data in session
    $_SESSION['user'] = $user;
    return true;
  }

  public function signout() {
    session_destroy();
  }

  public function isBanUser(string $sessionId) {
    return $this->SessionModel->isBanById(session_id());
  }

  public function addUser(array $set): int {
    return $this->UserModel
      ->set($set)
      ->insert();
  }

  public function updateUser(int $id, array $set) {
    $this->UserModel
      ->set($set)
      ->where('id', $id)
      ->update();
  }

  public function deleteUser(int $id) {
    $this->UserModel
      ->where('id', $id)
      ->delete();
  }
}