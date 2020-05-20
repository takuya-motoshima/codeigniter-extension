<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Util\Logger;

class SessionModel extends \AppModel {

  const TABLE = 'session';

  // public function deleteSessionOtherThanYourself(string $username, string $id) {
  //   parent
  //     ::where('username', $username)
  //     ::where('id !=', $id)
  //     ->delete();
  // }

  public function updateSessionBanFlagOn($username, string $id) {
    parent
      ::set('ban', 1)
      ::where('username', $username)
      ::where('id !=', $id)
      ->update();
  }

  public function isBanById(string $id): bool {
    return parent
      ::where('id', $id)
      ::where('ban', 1)
      ::count_all_results() > 0;
  }
}