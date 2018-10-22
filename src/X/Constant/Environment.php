<?php
/**
 * Environment constant
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Constant;

/**
 * @var sting ENV_PRODUCTION
 */
define('X_APP_PATH', preg_replace('/[^\/]+$/', '', __DIR__));

/**
 * @var sting ENV_PRODUCTION
 */
const ENV_PRODUCTION = 'production';

/**
 * @var sting ENV_TESTING
 */
const ENV_TESTING = 'testing';

/**
 * @var sting ENV_DEVELOPMENT
 */
const ENV_DEVELOPMENT = 'development';