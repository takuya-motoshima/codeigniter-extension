<?php
/**
 * Initialize the database
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @link    https://codeigniter.com/user_guide/database/
 * @param   string|string[] $config
 * @param   null|bool $queryBuilderOverride
 * @return  object
 */
namespace X\Database;
function &DB($config = '', $queryBuilderOverride = null) {
    // Load the DB config file if a DSN string wasn't passed
    if (is_string($config) && strpos($config, '://') === false) {
        // Is the config file in the environment folder?
        if ( ! file_exists($dbConfigPath = \APPPATH.'config/'.ENVIRONMENT.'/database.php')
            && ! file_exists($dbConfigPath = \APPPATH.'config/database.php'))
        {
            show_error('The configuration file database.php does not exist.');
        }

        include($dbConfigPath);

        // Make packages contain database config files,
        // given that the controller instance already exists
        if (class_exists('\CI_Controller', false)) {
            foreach (\get_instance()->load->get_package_paths() as $path) {
                if ($path !== \APPPATH) {
                    if (file_exists($dbConfigPath = $path.'config/'.ENVIRONMENT.'/database.php')) {
                        include($dbConfigPath);
                    } elseif (file_exists($dbConfigPath = $path.'config/database.php')) {
                        include($dbConfigPath);
                    }
                }
            }
        }
        if ( ! isset($db) OR count($db) === 0) {
            show_error('No database connection settings were found in the database config file.');
        }
        if ($config !== '') {
            $activeGroup = $config;
        }
        if ( ! isset($activeGroup)) {
            show_error('You have not specified a database connection group via $activeGroup in your config/database.php file.');
        } elseif ( ! isset($db[$activeGroup])) {
            show_error('You have specified an invalid database connection group ('.$activeGroup.') in your config/database.php file.');
        }
        $config = $db[$activeGroup];
    } elseif (is_string($config)) {
        /**
         * Parse the URL from the DSN string
         * Database settings can be passed as discreet
         * parameters or as a data source name in the first
         * parameter. DSNs must have this prototype:
         * $dsn = 'driver://username:password@hostname/database';
         */
        if (($dsn = parse_url($config)) === false) {
            show_error('Invalid DB Connection String');
        }
        $config = array(
            'dbdriver'  => $dsn['scheme'],
            'hostname'  => isset($dsn['host']) ? rawurldecode($dsn['host']) : '',
            'port'      => isset($dsn['port']) ? rawurldecode($dsn['port']) : '',
            'username'  => isset($dsn['user']) ? rawurldecode($dsn['user']) : '',
            'password'  => isset($dsn['pass']) ? rawurldecode($dsn['pass']) : '',
            'database'  => isset($dsn['path']) ? rawurldecode(substr($dsn['path'], 1)) : ''
        );
        // Were additional config items set?
        if (isset($dsn['query'])) {
            parse_str($dsn['query'], $extra);
            foreach ($extra as $key => $val) {
                if (is_string($val) && in_array(strtoupper($val), array('TRUE', 'FALSE', 'NULL'))) {
                    $val = var_export($val, true);
                }
                $config[$key] = $val;
            }
        }
    }

    // No DB specified yet? Beat them senseless...
    if (empty($config['dbdriver'])) {
        show_error('You have not selected a database type to connect to.');
    }

    // Load the DB classes. Note: Since the query builder class is optional
    // we need to dynamically create a class that extends proper parent class
    // based on whether we're using the query builder class or not.
    if ($queryBuilderOverride !== null) {
        $queryBuilder = $queryBuilderOverride;
    } elseif ( ! isset($queryBuilder) && isset($activeRecord)) {
        // Backwards compatibility work-around for keeping the
        // $activeRecord config variable working. Should be
        // removed in v3.1
        $queryBuilder = $activeRecord;
    }
    require_once(\BASEPATH.'database/DB_driver.php');
    if ( ! isset($queryBuilder) OR $queryBuilder === true) {
        require_once(\BASEPATH.'database/DB_query_builder.php');
        if ( ! class_exists('\CI_DB', false)) {
            /**
             * CI_DB
             *
             * Acts as an alias for both CI_DB_driver and CI_DB_query_builder.
             *
             * @see CI_DB_query_builder
             * @see CI_DB_driver
             */
            eval('class CI_DB extends \X\Database\QueryBuilder { }');
        }
    } elseif ( ! class_exists('\CI_DB', false)) {
        /**
         * @ignore
         */
        eval('class CI_DB extends \CI_DB_driver { }');
    }

    // Load the DB driver
    $driver_file = \BASEPATH.'database/drivers/'.$config['dbdriver'].'/'.$config['dbdriver'].'_driver.php';

    file_exists($driver_file) OR show_error('Invalid DB driver');
    require_once($driver_file);

    // Instantiate the DB adapter
    $driver = '\CI_DB_'.$config['dbdriver'].'_driver';
    $DB = new $driver($config);

    // Check for a subdriver
    if ( ! empty($DB->subdriver)) {
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