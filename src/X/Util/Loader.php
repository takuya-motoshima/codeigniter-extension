<?php
namespace X\Util;
use \X\Util\Logger;

/**
 * Various CI class loaders.
 */
final class Loader {
  /**
   * Load model.
   * @param string|string[] $models Model Name.
   * @return void
   */
  public static function model($models): void {
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
   * @param string|string[] $libraries Library Name.
   * @return void
   */
  public static function library($libraries): void {
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
   * @param mixed $config (optional) Connection group name or database configuration options. Default is "default".
   * @param bool $return (optional) Whether to return a DB instance. Default is false.
   * @param mixed $queryBuilder (optional) An instance that overrides the existing CI_DB_query_builder.
   * @param bool $overwrite (optional) Whether to overwrite the DB of the global CI_Controller instance with the DB instance generated this time. Default is false.
   * @return \X\Database\DB|null|false Database object if return is set to true, false if return fails, null otherwise.
   */
  public static function database($config='default', bool $return=false, $queryBuilder=null, bool $overwrite=false) {
    $CI =& \get_instance();

    // Do we even need to load the database class?
    if (!$return && $queryBuilder === null && isset($CI->db) && is_object($CI->db) && !empty($CI->db->conn_id) && !$overwrite)
      return false;
    $db = \X\Database\DB($config, $queryBuilder);
    if (!$return || $overwrite) {
      // Initialize the db variable. Needed to prevent reference errors with some configurations.
      $CI->db = '';

      // Load the DB class.
      $CI->db =& $db;
    }
    if ($return)
      return $db;
    return null;
  }

  /**
   * Load config.
   * @param string $configFile Name of the config file.
   * @param string|null $itemName (optional) The name of the item in the config file to be retrieved. If omitted, an object with all items in the config file is obtained.
   * @return mixed Config data.
   */
  public static function config(string $configFile, string $itemName=null) {
    static $config;
    if (isset($config[$configFile])) {
      if (empty($itemName))
        return $config[$configFile];
      return $config[$configFile][$itemName] ?? '';
    }
    $CI =& \get_instance();
    $CI->config->load($configFile, true);
    $config[$configFile] = $CI->config->item($configFile);
    if (empty($itemName))
      return $config[$configFile];
    return $config[$configFile][$itemName] ?? '';
  }
}