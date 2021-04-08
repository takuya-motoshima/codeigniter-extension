<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\Access;
use \X\Util\Logger;

/**
 * Batch with file lock.
 */
class ChildBatch1 extends AppController {

  private $tag;
  private $lockfile;
  private $fp;

  function __construct() {
    parent::__construct();
    // Lock file path.
    $this->lockfile = __DIR__ . '/.lock';

    // Enable signal monitoring.
    pcntl_async_signals(true);

    // Disable pause (ctrl + z).
    pcntl_signal(SIGTSTP, SIG_IGN);

    // Delete the lock file when forced termination (ctrl + c).
    pcntl_signal(SIGINT, function() {
      $this->unlock();
      $this->debug('Suspended.');
      exit;
    });
  }

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

    // Unlock.
    $this->unlock();
    $this->debug('Completed');
  }

  /**
   * Lock.
   * Returns false if locked.
   */
  private function lock(): bool {
    // Open lock file.
    $this->fp = fopen($this->lockfile, 'a');

    // Returns false if locked.
    if (!flock($this->fp, LOCK_EX|LOCK_NB)) return false;
    return true;
    // touch($this->lockfile);
  }

  /**
   * Unlock.
   */
  private function unlock() {
    if (is_resource($this->fp)) fclose($this->fp);
    // if (file_exists($this->lockfile)) unlink($this->lockfile);
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