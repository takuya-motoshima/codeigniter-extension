<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Util\Logger;

class QueryCache extends AppController {
  protected $model = 'UserModel';
  public function index() {
    try {
      // Cache the results of this search query.
      // The cache is saved in the directory specified in cachedir in "config/database.php".
      $this->UserModel->cache_on();

      // Find user.
      // If there is no cache yet, "QueryCacheTest+index/7f2b1a5f6e58f60d11f06c1635f55c17" will be created in the cache directory, and the contents will be as follows.
      // O:12:"CI_DB_result":8:{s:7:"conn_id";N;s:9:"result_id";N;s:12:"result_array";a:1:{i:0;a:2:{s:2:"id";s:1:"1";s:4:"name";s:5:"Robin";}}s:13:"result_object";a:1:{i:0;O:8:"stdClass":2:{s:2:"id";s:1:"1";s:4:"name";s:5:"Robin";}}s:20:"custom_result_object";a:0:{}s:11:"current_row";i:0;s:8:"num_rows";i:1;s:8:"row_data";N;}
      $user = $this->UserModel
        ->select('id, name')
        ->where('id', 1)
        ->get()
        ->row_array();

      // Disable the cache.
      $this->UserModel->cache_off();

      Logger::print($user);
    } catch (\Throwable $e) {
      Logger::print($e->getMessage());
    }
  }

  public function deleteCache() {
    $this->UserModel->cache_delete('QueryCacheTest', 'index');
  }
}