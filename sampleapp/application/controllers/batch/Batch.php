<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\Access;
use \X\Util\Logger;

/**
 * This controller was added to test batch locks.
 */
class Batch extends AppController {

  private $tag;

  /**
   * @Access(allow_http=false)
   */
  public function run(string $tag, int $runtime) {
    // The name of this batch.
    $this->tag = $tag;

    // Wait until the specified time to execute at the same time as other batches.
    time_sleep_until($runtime);

    // Start main processing.
    $this->debug('Start');
    $this->debug('Completed');
  }

  /**
   * Output log.
   */
  private function debug(...$params) {
    $message = $this->tag . ': ';
    foreach ($params as $param) {
      if (is_array($param) || is_object($param)) $message .= print_r($param, true);
      else $message .= $param;
    }
    Logger::debug($message);
  }
}