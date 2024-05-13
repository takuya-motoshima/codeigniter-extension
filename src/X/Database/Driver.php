<?php
namespace X\Database;
use \X\Util\Logger;

/**
 * Database Driver Class.
 */
#[\AllowDynamicProperties]
abstract class Driver extends \CI_DB_driver {}