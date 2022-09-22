<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Util\Logger;
use \X\Util\Cipher;
use \X\Util\ImageHelper;
use \X\Util\FileHelper;

class UserModel extends \AppModel {
  const TABLE = 'user';

  public function login(string $email, string $password): bool {
    $user = $this
      ->where('email', $email)
      ->where('password', Cipher::encode_sha256($password))
      ->get()
      ->row_array();
    if (empty($user))
      return false;
    unset($user['password']);
    $_SESSION[SESSION_NAME] = $user;
    return true;
  }

  public function logout(): bool {
    unset($_SESSION[SESSION_NAME]);
    return true;
  }

  public function paginate(int $offset, int $limit, string $order, string $direction, ?array $search): array {
    function setWhere(CI_Model $model, ?array $search) {
      if (!empty($search['keyword']))
        $model
          ->group_start()
          ->or_like('email', $search['keyword'])
          ->or_like('name', $search['keyword'])
          ->group_end();
    }
    setWhere($this, $search);
    $rows = $this
      ->select('id, role, email, name, modified')
      ->order_by($order, $direction)
      ->limit($limit, $offset)
      ->get()
      ->result_array();
    setWhere($this, $search);
    $recordsFiltered = $this->count_all_results();
    $recordsTotal = $this->count_all_results();
    return ['recordsTotal' => $recordsTotal, 'recordsFiltered' => $recordsFiltered, 'data' => $rows];
  }

  public function createUser(array $set) {
    try {
      // Logger::debug('$set=', $set);
      parent::trans_begin();
      $userId = $this
        ->set('role', $set['role'])
        ->set('email', $set['email'])
        ->set('name', $set['name'])
        ->set('password', Cipher::encode_sha256($set['password']))
        ->insert();
      $this->writeUserIconImage($userId, $set['icon']);
      parent::trans_commit();
    } catch (\Throwable $e) {
      parent::trans_rollback();
      throw $e;
    }
  }

  public function emailExists(string $email, int $excludeUserId = null): bool {
    // Logger::debug('$email=', $email, ', $excludeUserId=', $excludeUserId);
    if (!empty($excludeUserId))
      $this->where('id !=', $excludeUserId);
    return $this
      ->where('email', $email)
      ->count_all_results() > 0;
  }

  public function getUserById(int $userId): ?array {
    return $this
      ->select('id, role, email, name, created, modified')
      ->where('id', $userId)
      ->get()
      ->row_array();
  }

  public function updateUser(int $userId, array $set) {
    try {
      parent::trans_begin();
      if (!$this->userIdExists($userId))
        throw new UserNotFoundException();
      if (!empty($set['changePassword'])) {
        $this->set('password', Cipher::encode_sha256($set['password']));
        Logger::debug("Change the password whose user ID is {$userId}");
      }
      $this
        ->set('role', $set['role'])
        ->set('email', $set['email'])
        ->set('name', $set['name'])
        // NOTE: If the record is not changed and only the image is changed, the modification date is not updated.
        // Explicitly update the modification date.
        ->set('modified', 'NOW()', FALSE)
        ->where('id', $userId)
        ->update();
      $this->writeUserIconImage($userId, $set['icon']);
      parent::trans_commit();
    } catch (\Throwable $e) {
      parent::trans_rollback();
      throw $e;
    }
  }

  public function deleteUser(int $userId) {
    try {
      if (!$this->userIdExists($userId))
        throw new UserNotFoundException();
      parent::trans_begin();
      $this
        ->where('id', $userId)
        ->delete();
      $this->deleteUserIconImage($userId);
      parent::trans_commit();
    } catch (\Throwable $e) {
      parent::trans_rollback();
      throw $e;
    }
  }

  private function writeUserIconImage(int $userId, string $dataUrl) {
    $filePath = FCPATH . "upload/{$userId}.png";
    ImageHelper::putBase64($dataUrl, $filePath);
    Logger::debug("Write {$filePath}");
  }

  private function userIdExists(int $userId): bool {
    return $this
      ->where('id', $userId)
      ->count_all_results() > 0;
  }

  private function deleteUserIconImage(int $userId) {
    $filePath = FCPATH . "upload/{$userId}.png";
    FileHelper::delete($dataUrl, $filePath);
    Logger::debug("Delete {$filePath}");
  }
}