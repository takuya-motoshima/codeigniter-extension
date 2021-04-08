<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Util\Logger;

class UserService extends \AppModel {

  protected $model = 'UserModel';

  /**
   * Authenticate with username and password.
   */
  public function login(string $email, string $password): bool {
    // Find a user that matches your ID.
    $user = $this->UserModel
      ->where('email', $email)
      ->where('password', $password)
      ->get()
      ->row_array();

    // Login fails if no user is found.
    if (empty($user))
      return false;

    // Store login user data in session
    unset($user['password']);
    $_SESSION['session'] = $user;
    return true;
  }

  /**
   * Log out.
   */
  public function logout() {
    session_destroy();
  }

  /**
   * Add user.
   * @param array $set [description]
   */
  public function addUser(array $set): int {
    return $this->UserModel
      ->set($set)
      ->insert();
  }

  /**
   * Update user.
   */
  public function updateUser(int $id, array $set) {
    $this->UserModel
      ->set($set)
      ->where('id', $id)
      ->update();
  }

  /**
   * Delete user.
   */
  public function deleteUser(int $id) {
    $this->UserModel
      ->where('id', $id)
      ->delete();
  }

  /**
   * Returns the user that matches the ID.
   */
  public function getUserById(int $id): ?array {
    return $this->UserModel
      ->where('id', $id)
      ->get()
      ->row_array();
  }

  /**
   * Returns all users.
   */
  public function getUsers(): array {
    return $this->UserModel
      ->select('id, role, email, name, modified')
      ->get()
      ->result_array();
  }

  /**
   * Returns the data of the specified page number.
   */
  public function paginate(int $offset, int $limit, string $orderby, string $direction, ?string $search): array {
    /**
     * Set search conditions for list search and overall count.
     */
    function setWhere(CI_Model $model, ?string $search) {
      // Exit if no search word is specified.
      if (empty($search)) return;
      // Filter by search word.
      $model
        ->group_start()
        ->or_like('id', $search)
        ->or_like('role', $search)
        ->or_like('email', $search)
        ->or_like('name', $search)
        ->or_like('modified', $search)
        ->group_end();
    }

    // Display data.
    setWhere($this->UserModel, $search);
    $rows = $this->UserModel
      ->select('id, role, email, name, modified')
      ->order_by($orderby, $direction)
      ->limit($limit, $offset)
      ->get()
      ->result_array();

    // Total number of data matching the search conditions.
    setWhere($this->UserModel, $search);
    $recordsFiltered = $this->UserModel->count_all_results();

    // Number of all data.
    $recordsTotal = $this->UserModel->count_all_results();
    return [
      'recordsTotal' => $recordsTotal,
      'recordsFiltered' => $recordsFiltered,
      'data' => $rows
    ];
  }
}