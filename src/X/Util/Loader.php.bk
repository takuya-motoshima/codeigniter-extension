<?php
/**
 * Load util class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Util;
use \X\Util\Logger;
final class Loader {

  /**
   * Load model
   *
   * @param  string|array $models
   * @return void
   */
  public static function model($models) {
    if (empty($models)) {
      return;
    }
    if (is_string($models)) {
      $models = [$models];
    }
    $ci =& \get_instance();
    foreach ($models as $model) {
      $ci->load->model($model);
    }
  }

  /**
   * Load library
   *
   * @param  string|array $models
   * @return void
   */
  public static function library($libraries) {
    if (empty($libraries)) {
      return;
    }
    if (is_string($libraries)) {
      $libraries = [$libraries];
    }
    $ci =& \get_instance();
    foreach ($libraries as $library) {
      $ci->load->library($library);
    }
  }

  /**
   * Load databse
   *
   * @param   string|string[] $databaseConfig
   * @param   bool $return
   * @param   null|bool $queryBuilder
   * @return  object|null
   */
  public static function database($databaseConfig = '', bool $return = false, $queryBuilder = null) {
    $ci =& \get_instance();
    if ($return === false && $queryBuilder === null && isset($ci->db) && is_object($ci->db) && !empty($ci->db->conn_id)) {
      return;
    }
    if ($return === true) {
      return \X\Database\DB($databaseConfig, $queryBuilder);
    }
    $ci->db = '';
    $ci->db =& \X\Database\DB($databaseConfig, $queryBuilder);
  }

  /**
   * Load config
   *
   * @param   string $configName
   * @param   string $configeKey
   * @return  array
   */
  public static function config(string $configName, string $configeKey = null) {
    static $config;
    if (isset($config[$configName])) {
      if (empty($configeKey)) {
        return $config[$configName];
      }
      return $config[$configName][$configeKey] ?? '';
    }
    $ci =& \get_instance();
    $ci->config->load($configName, true);
    $config[$configName] = $ci->config->item($configName);
    if (empty($configeKey)) {
      return $config[$configName];
    }
    return $config[$configName][$configeKey] ?? '';
  }
}