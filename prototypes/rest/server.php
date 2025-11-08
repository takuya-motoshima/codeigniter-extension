<?php
ini_set('log_errors', 'on');
ini_set('error_log', 'logs/' . date('Ymd') . '.log');

// Check request headers.
$headers = getallheaders();
error_log('Request header:' . print_r($headers, true));

// Return message.
echo json_encode(['message' => 'Server successfully received']);