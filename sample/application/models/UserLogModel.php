<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Util\Logger;
use \X\Util\IpUtils;

class UserLogModel extends \AppModel {
  const TABLE = 'userLog';

  public function createUserLog(string $name, string $message) {
    try {
      $this
        ->set('name', $name)
        ->set('message', $message)
        ->set('ip', IpUtils::getClientIpFromXFF())
        ->insert();
    } catch (\Throwable $e) {
      Logger::error($e);
    }
  }

  public function paginate(int $offset, int $limit, string $order, string $direction, ?array $search): array {
    function setWhere(CI_Model $model, ?array $search) {
      if (!empty($search['name']))
        $model->where('name', $search['name']);
    }
    setWhere($this, $search);
    $rows = $this
      ->select('name, message, ip, created')
      ->order_by($order, $direction)
      ->limit($limit, $offset)
      ->get()
      ->result_array();
    Logger::debug($this->last_query());
    setWhere($this, $search);
    $recordsFiltered = $this->count_all_results();
    $recordsTotal = $this->count_all_results();
    return ['recordsTotal' => $recordsTotal, 'recordsFiltered' => $recordsFiltered, 'data' => $rows];
  }

  public function getUsernameOptions(): array {
    return $this
      ->select('name')
      ->group_by('name')
      ->get()
      ->result_array();
  }
}