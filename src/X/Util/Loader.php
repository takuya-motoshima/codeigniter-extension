<?php
namespace X\Util;
use \X\Util\Logger;

final class Loader {
  /**
   * Load model.
   */
  public static function model($models) {
    if (empty($models))
      return;
    if (is_string($models))
      $models = [$models];
    $ci =& \get_instance();
    foreach ($models as $model)
      $ci->load->model($model);
  }

  /**
   * Load library.
   */
  public static function library($libraries) {
    if (empty($libraries))
      return;
    if (is_string($libraries))
      $libraries = [$libraries];
    $ci =& \get_instance();
    foreach ($libraries as $library)
      $ci->load->library($library);
  }

  /**
   * Load databse.
   */
  public static function database($config = 'default', bool $return = false, $queryBuilder = null, bool $overwrite = false) {
    $ci =& \get_instance();
    if (!$return && $queryBuilder === null
        && isset($ci->db)
        && is_object($ci->db)
        && !empty($ci->db->conn_id)
        && !$overwrite)
      return;
    $db = \X\Database\DB($config, $queryBuilder);
    if (!$return || $overwrite) {
      $ci->db = '';
      $ci->db =& $db;
    }
    if ($return)
      return $db;
  }

  /**
   * Load config.
   */
  public static function config(string $configName, string $configeKey = null) {
    static $config;
    if (isset($config[$configName])) {
      if (empty($configeKey))
        return $config[$configName];
      return $config[$configName][$configeKey] ?? '';
    }
    $ci =& \get_instance();
    $ci->config->load($configName, true);
    $config[$configName] = $ci->config->item($configName);
    if (empty($configeKey))
      return $config[$configName];
    return $config[$configName][$configeKey] ?? '';
  }
}