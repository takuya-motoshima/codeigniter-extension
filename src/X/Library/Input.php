<?php
namespace X\Library;
use \X\Util\HttpInput;

/**
 * CI_Input extension.
 */
abstract class Input extends \CI_Input {
  /**
   * Fetch an item from the PUT array.
   * @param mixed|null $index (optional) Index for item to be fetched from $array. Default is null.
   * @param bool|null $xssClean (optional) Whether to apply XSS filtering. Default is null.
   * @return mixed PUT data.
   */
  public function put($index=null, $xssClean=null) {
    return HttpInput::put($index, $xssClean);
  }

  /**
   * Fetch an item from the DELETE array.
   * @param mixed|null $index (optional) Index for item to be fetched from $array. Default is null.
   * @param bool|null $xssClean (optional) Whether to apply XSS filtering. Default is null.
   * @return mixed DELETE data.
   */
  public function delete($index=null, $xssClean=null) {
    return parent::input_stream($index, $xssClean);
  }
}