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
use \X\Util\Validation;

abstract class FormValidation extends \CI_Form_validation {

  // private $format = 'd-m-Y H:i:s';
  // private $my_error_messages = [];

  /**
   * Initialize Form_Validation class
   *
   * @param array $rules
   * @return  void
   */
  function __construct($rules = []) {
    parent::__construct($rules);
    // $this->my_error_messages['form_validation_datetime'] = 'The {field} field must have format(' . $this->format . ').';
  }

  /**
  * Validate datetime.
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
  * @param  string $input  Input value.
  * @param  string $format Date format
  * @return bool           Returns true if validation succeeds, false if validation fails.
  */
  public function datetime(string $input, string $format): bool {
    $input = str_replace(['-', '/'], '-', $input);
    if (date($format, strtotime($input)) == $input)
      return true;
    // $this->format = $format;
    $this->set_message('datetime', "The {field} field must have format $format.");
    return false;
  }

  /**
   * Validate hostname.
   * 
   * @param  string $input Input value.
   * @return bool          Returns true if validation succeeds, false if validation fails.
   */
  public function hostname(string $input): bool {
    if (Validation::hostname($input)) return true;
    # if (preg_match('/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]$/', $input)) return true;
    $this->set_message('hostname', 'The {field} field must contain a valid host name.');
    return false;
  }

  /**
   * Validate ip address.
   * 
   * @param  string $input Input value.
   * @return bool          Returns true if validation succeeds, false if validation fails.
   */
  public function ipaddress(string $input): bool {
    if (Validation::ipaddress($input)) return true;
    $this->set_message('ipaddress', 'The {field} field must contain a valid ip address.');
    return false;
  }

  /**
   * Validate hostname or ip address.
   * 
   * @param  string $input Input value.
   * @return bool          Returns true if validation succeeds, false if validation fails.
   */
  public function hostname_or_ipaddress(string $input): bool {
    if (Validation::hostname_or_ipaddress($input)) return true;
    # if (preg_match('/^((([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])|((?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]))$/', $input)) return true;
    $this->set_message('hostname_or_ipaddress', 'The {field} field must contain a valid host name or ip address.');
    return false;
  }

  /**
   * Validate UNIX username.
   * 
   * @param  string $input Input value.
   * @return bool          Returns true if validation succeeds, false if validation fails.
   */
  public function unix_username(string $input): bool {
    if (Validation::unix_username($input)) return true;
    $this->set_message('unix_username', 'The {field} field must contain a valid UNIX username.');
    return false;
  }

  /**
   * Validate port number.
   * 
   * @param  string $input Input value.
   * @return bool          Returns true if validation succeeds, false if validation fails.
   */
  public function port(string $input): bool {
    if (Validation::port($input)) return true;
    $this->set_message('port', 'The {field} field must contain a valid port number.');
    return false;
  }
}