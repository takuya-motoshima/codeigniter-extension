<?php
/**
 * Request data validation class.
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2020 Takuya Motoshima
 */
namespace X\Library;
use \X\Util\Logger;

abstract class FormValidation extends \CI_Form_validation {

  private $format = 'd-m-Y H:i:s';
  private $my_error_messages = [];

  /**
   * Initialize Form_Validation class
   *
   * @param array $rules
   * @return  void
   */
  function __construct($rules = []) {
    parent::__construct($rules);
    $this->my_error_messages['form_validation_datetime'] = 'The {field} field must have format(' . $this->format . ').';
  }

  /**
  * Validate Datetime.
  *
  * @example
  * $this->form_validation
  *   ->set_data(['datetime' => '2021-02-03 17:46:00'])
  *   ->set_rules('datetime', 'datetime', 'required|datetime[Y-m-d H:i:s]');
  * if ($this->form_validation->run() != false) {
  *   // put your code here
  * } else {
  *   echo validation_errors();
  * }
  * 
  * @param  string $date
  * @param  string $value
  * @return bool
  */
  public function datetime($date, $value): bool {
    $date = str_replace(['-', '/'], '-', $date);
    if (date($value, strtotime($date)) == $date) return true;
    $this->format = $value;
    $this->set_message('datetime', 'The {field} field must have format ' . $this->format);
    return false;
  }
}