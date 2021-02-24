<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\Access;
use \X\Util\Logger;

/**
 * Batch with advisory lock.
 * Locks the program file itself to be executed.
 */
class AdvisoryLockBatch extends AppController {

  private $tag;

  /**
   * @Access(allow_http=false)
   */
  public function run(string $tag, int $runtime = 0) {
    // The name of this batch.
    $this->tag = $tag;

    // Wait until the specified time to execute at the same time as other batches.
    if ($runtime > 0) time_sleep_until($runtime);

    // Start main processing.
    $this->debug('Start');

    // Lock.
    if (!$this->lock()) {
      $this->debug('Unable lock.');
      exit(0);
    }

    // Wait.
    sleep(3);

    $this->debug('Completed');
  }

  /**
   * Lock.
   * Returns false if locked.
   */
  private function lock(): bool {
    static $fp;

    // Open the lock file.
    // This batch file itself.
    $fp = fopen(__FILE__, 'r');

    // Returns an error if the file cannot be read.
    if (!is_resource($fp)) throw new \RuntimeException('Unable open ' . __FILE__);

    // A flag indicating whether the lock is blocked.True if the lock is blocked.
    $wouldBlock = false;

    // Lock the batch file itself.
    // "LOCK_EX" is an exclusive lock.
    // If "LOCK_NB" is not set, wait until the lock is released.
    if (flock($fp, LOCK_EX|LOCK_NB, $wouldBlock) == false) {
      // Returns false if locked.
      if ($wouldBlock) return false;
    }

    // Returns true if the lock is successful.
    return true;
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