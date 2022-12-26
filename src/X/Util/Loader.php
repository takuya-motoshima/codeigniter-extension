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
    $CI =& \get_instance();
    foreach ($models as $model)
      $CI->load->model($model);
  }

  /**
   * Load library.
   */
  public static function library($libraries) {
    if (empty($libraries))
      return;
    if (is_string($libraries))
      $libraries = [$libraries];
    $CI =& \get_instance();
    foreach ($libraries as $library)
      $CI->load->library($library);
  }

  /**
   * Load databse.
   */
  public static function database($config = 'default', bool $return = false, $queryBuilder = null, bool $overwrite = false) {
    $CI =& \get_instance();
    if (!$return && $queryBuilder === null
        && isset($CI->db)
        && is_object($CI->db)
        && !empty($CI->db->conn_id)
        && !$overwrite)
      return;
    $db = \X\Database\DB($config, $queryBuilder);
    if (!$return || $overwrite) {
      $CI->db = '';
      $CI->db =& $db;
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
    $CI =& \get_instance();
    $CI->config->load($configName, true);
    $config[$configName] = $CI->config->item($configName);
    if (empty($configeKey))
      return $config[$configName];
    return $config[$configName][$configeKey] ?? '';
  }
}