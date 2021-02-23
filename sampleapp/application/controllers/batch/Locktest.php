<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\Access;
use \X\Util\Logger;

/**
 * Test batch lock.
 */
class Locktest extends AppController {

  /**
   * @Access(allow_http=false)
   */
  public function run() {
    // Executable file.
    $exefile = FCPATH . 'index.php';

    // Environment name.
    $env = ENVIRONMENT;

    // Time when the batch called from here executes processing.
    $runtime = time() + 1;

    // Launch multiple batches.
    exec("CI_ENV={$env} php {$exefile} batch/batch/run/A/{$runtime} > /dev/null &");
    exec("CI_ENV={$env} php {$exefile} batch/batch/run/B/{$runtime} > /dev/null &");
  }
}