<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Util\Logger;
use \X\Util\Cipher;
use \X\Util\ImageHelper;

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
      Logger::debug('$set=', $set);
      parent::trans_begin();
      $userId = $this
        ->set('role', $set['role'])
        ->set('name', $set['name'])
        ->set('email', $set['email'])
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
    Logger::debug('$email=', $email);
    Logger::debug('$excludeUserId=', $excludeUserId);
    if (!empty($excludeUserId))
      $this->where('id !=', $excludeUserId);
    return $this
      ->where('email', $email)
      ->count_all_results() > 0;
  }

  public function updateUser(int $id, array $set) {
    $this
      ->set($set)
      ->where('id', $id)
      ->update();
  }

  public function deleteUser(int $id) {
    $this
      ->where('id', $id)
      ->delete();
  }

  public function getUserById(int $id): ?array {
    return $this
      ->where('id', $id)
      ->get()
      ->row_array();
  }

  public function getUsers(): array {
    return $this
      ->select('id, role, email, name, modified')
      ->get()
      ->result_array();
  }

  private function writeUserIconImage(int $userId, string $dataUrl) {
    $filePath = FCPATH . "upload/{$userId}.png";
    ImageHelper::putBase64($dataUrl, $filePath);
    Logger::debug("Write {$filePath}");
  }
}