<?php
namespace X\Constant;

define('X_APP_PATH', preg_replace('/[^\/]+$/', '', __DIR__));
const ENV_PRODUCTION = 'production';
const ENV_TESTING = 'testing';
const ENV_DEVELOPMENT = 'development';