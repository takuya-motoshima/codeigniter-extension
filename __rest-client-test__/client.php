<?php
/**
 * ```sh
 * php __rest-client-test__/client.php
 * ```
 */
require __DIR__ . '/../src/X/Util/RestClient.php';
require __DIR__ . '/../src/X/Util/Logger.php';

$client = new \X\Util\RestClient([
  'base_url' => 'http://localhost:9000/server.php',
  'debug' => true,
  'headers' => [
    'X-My-Key' => 'foo',
  ],
]);

// Send request with custom headers.
$client->get('/');