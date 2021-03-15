<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Annotation\Access;
use \X\Util\Logger;

/**
 * Launch multiple batches.
 */
class RunMultipleBatch extends AppController {

  /**
   * @Access(allow_http=false)
   */
  public function run(string $locktype) {

    // Executable file.
    $exefile = FCPATH . 'index.php';

    // Environment name.
    $env = ENVIRONMENT;

    // Time when the batch called from here executes processing.
    $runtime = time() + 2;

    // Batch file name to execute.
    if ($locktype === 'filelock') $batchfile = 'fileLockBatch';
    else if ($locktype === 'advisorylock') $batchfile = 'advisoryLockBatch';
    else throw new RuntimeException('The parameter locktype is incorrect.');

    // Launch multiple batches.
    exec("CI_ENV={$env} php {$exefile} batch/{$batchfile}/run/a/{$runtime} > /dev/null &");
    exec("CI_ENV={$env} php {$exefile} batch/{$batchfile}/run/b/{$runtime} > /dev/null &");
    exec("CI_ENV={$env} php {$exefile} batch/{$batchfile}/run/c/{$runtime} > /dev/null &");
  }
}