<?php
namespace X\Database;

function &DB($config = '', $queryBuilderOverride = null) {
  if (is_string($config) && strpos($config, '://') === false) {
    if (!file_exists($dbConfigPath = \APPPATH.'config/'.ENVIRONMENT.'/database.php')
      && !file_exists($dbConfigPath = \APPPATH.'config/database.php'))
      show_error('The configuration file database.php does not exist.');
    include($dbConfigPath);
    if (class_exists('\CI_Controller', false)) {
      foreach (\get_instance()->load->get_package_paths() as $path) {
        if ($path !== \APPPATH) {
          if (file_exists($dbConfigPath = $path.'config/'.ENVIRONMENT.'/database.php'))
            include($dbConfigPath);
          elseif (file_exists($dbConfigPath = $path.'config/database.php'))
            include($dbConfigPath);
        }
      }
    }
    if (!isset($db) OR count($db) === 0)
      show_error('No database connection settings were found in the database config file.');
    if ($config !== '')
      $activeGroup = $config;
    if (!isset($activeGroup))
      show_error('You have not specified a database connection group via $activeGroup in your config/database.php file.');
    elseif (!isset($db[$activeGroup]))
      show_error('You have specified an invalid database connection group ('.$activeGroup.') in your config/database.php file.');
    $config = $db[$activeGroup];
  } elseif (is_string($config)) {
    if (($dsn = parse_url($config)) === false)
      show_error('Invalid DB Connection String');
    $config = array(
      'dbdriver'  => $dsn['scheme'],
      'hostname'  => isset($dsn['host']) ? rawurldecode($dsn['host']) : '',
      'port'      => isset($dsn['port']) ? rawurldecode($dsn['port']) : '',
      'username'  => isset($dsn['user']) ? rawurldecode($dsn['user']) : '',
      'password'  => isset($dsn['pass']) ? rawurldecode($dsn['pass']) : '',
      'database'  => isset($dsn['path']) ? rawurldecode(substr($dsn['path'], 1)) : ''
    );
    if (isset($dsn['query'])) {
      parse_str($dsn['query'], $extra);
      foreach ($extra as $key => $val) {
        if (is_string($val) && in_array(strtoupper($val), array('TRUE', 'FALSE', 'NULL')))
          $val = var_export($val, true);
        $config[$key] = $val;
      }
    }
  }
  if (empty($config['dbdriver']))
    show_error('You have not selected a database type to connect to.');
  if ($queryBuilderOverride !== null)
    $queryBuilder = $queryBuilderOverride;
  elseif (!isset($queryBuilder) && isset($activeRecord))
    $queryBuilder = $activeRecord;
  require_once(\BASEPATH.'database/DB_driver.php');
  if (!isset($queryBuilder) OR $queryBuilder === true) {
    require_once(\BASEPATH.'database/DB_query_builder.php');
    if (!class_exists('\X_DB', false))
      eval('class X_DB extends \X\Database\QueryBuilder { }');
  } elseif (!class_exists('\X_DB', false))
    eval('class X_DB extends \CI_DB_driver { }');
  $driver = '\X\Database\Driver\\' . ucfirst($config['dbdriver']) . '\Driver';
  $DB = new $driver($config);
  if (!empty($DB->subdriver)) {
    $driver_file = \BASEPATH.'database/drivers/'.$DB->dbdriver.'/subdrivers/'.$DB->dbdriver.'_'.$DB->subdriver.'_driver.php';
    if (file_exists($driver_file)) {
      require_once($driver_file);
      $driver = '\CI_DB_'.$DB->dbdriver.'_'.$DB->subdriver.'_driver';
      $DB = new $driver($config);
    }
  }
  $DB->initialize();
  return $DB;
}