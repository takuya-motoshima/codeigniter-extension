<?php
namespace X\Core;

/**
 * Loader Class.
 */
#[\AllowDynamicProperties]
class Loader extends \CI_Loader {
  /**
   * Database Loader.
   * @param mixed $config (optional) Connection group name or database configuration options.
   * @param bool $return (optional) Whether to return a DB instance. Default is false.
   * @param mixed $queryBuilder (optional) An instance that overrides the existing CI_DB_query_builder.
   * @return \X\Database\DB|\X\Core\Loader|false Database object if $return is set to true, false on failure, Loader instance in any other case.
   */
  public function database($config='', $return=false, $queryBuilder=null) {
    $db = \X\Util\Loader::database(empty($config) ? 'default' : $config, $return, $queryBuilder);
    return $db !== null ? $db : $this;
  }
}
