<?php
ini_set('log_errors', 'on');
ini_set('error_log', 'logs/' . date('Ymd') . '.log');

// Get request headers.
$headers = getallheaders();
error_log('$headers=' . print_r($headers, true));