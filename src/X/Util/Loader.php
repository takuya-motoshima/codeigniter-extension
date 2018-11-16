<?php
/**
 * Load util class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Util;
final class Loader
{
  /**
   * Load model
   *
   * @param  string|array $models
   * @return void
   */
  public static function model($models)
  {
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
  public static function library($libraries)
  {
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
   * @param   string|string[] $config
   * @param   bool $return
   * @param   null|bool $queryBuilder
   * @return  object|null
   */
  public static function database($config = '', bool $return = false, $queryBuilder = null)
  {
    $ci =& \get_instance();
    if ($return === false && $queryBuilder === null && isset($ci->db) && is_object($ci->db) && !empty($ci->db->conn_id)) {
      return;
    }
    if ($return === true) {
      return \X\Database\DB($config, $queryBuilder);
    }
    $ci->db = '';
    $ci->db =& \X\Database\DB($config, $queryBuilder);
  }

  /**
   * Load config
   *
   * @param   string $file
   * @param   string $item
   * @return  array
   */
  public static function config(string $file, string $item = null)
  {
    static $config;
    if (isset($config[$file])) {
      if (empty($item)) {
        return $config[$file];
      }
      return $config[$file][$item];
    }
    $ci =& \get_instance();
    $ci->config->load($file, true);
    $config[$file] = $ci->config->item($file);
    if (empty($item)) {
      return $config[$file];
    }
    return $config[$file][$item];
  }
}