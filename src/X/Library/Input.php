<?php
namespace X\Library;
use \X\Util\Logger;
use \X\Util\HttpInput;

abstract class Input extends \CI_Input {
  /**
   * Fetch an item from the PUT array.
   */
  public function put($index = NULL, $xss_clean = NULL) {
    return HttpInput::put($index, $xss_clean);
  }

  /**
   * Fetch an item from the DELETE array.
   */
  public function delete($index = NULL, $xss_clean = NULL) {
    return parent::input_stream($index, $xss_clean);
  }
}