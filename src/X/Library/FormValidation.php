<?php
namespace X\Library;
use \X\Util\Logger;
use \X\Util\Validation;

abstract class FormValidation extends \CI_Form_validation {
  function __construct($rules = []) {
    parent::__construct($rules);
  }

  /**
  * Check if it is the date and time.
  * ```php
  * $this->form_validation
  *   ->set_data(['datetime' => '2021-02-03 17:46:00'])
  *   ->set_rules('datetime', 'datetime', 'required|datetime[Y-m-d H:i:s]');
  * if (!$this->form_validation->run()) {
  *   // Input error.
  *   ;
  * ```
  */
  public function datetime(string $input, string $format): bool {
    $input = str_replace(['-', '/'], '-', $input);
    if (date($format, strtotime($input)) == $input)
      return true;
    $this->set_message('datetime', "The {field} field must have format $format.");
    return false;
  }

  /**
   * Check if it is a host name.
   */
  public function hostname(string $input): bool {
    if (Validation::hostname($input))
      return true;
    $this->set_message('hostname', 'The {field} field must contain a valid host name.');
    return false;
  }

  /**
   * Check if it is an IP.
   */
  public function ipaddress(string $input): bool {
    if (Validation::ipaddress($input))
      return true;
    $this->set_message('ipaddress', 'The {field} field must contain a valid ip address.');
    return false;
  }

  /**
   * Check if it is IP or CIDR format.
   */
  public function ipaddress_or_cidr(string $input): bool {
    if (Validation::ipaddress_or_cidr($input))
      return true;
    $this->set_message('ipaddress_or_cidr', 'The {field} field must contain a valid ip address or CIDR.');
    return false;
  }

  /**
   * Check if it is a host name or IP.
   */
  public function hostname_or_ipaddress(string $input): bool {
    if (Validation::hostname_or_ipaddress($input))
      return true;
    $this->set_message('hostname_or_ipaddress', 'The {field} field must contain a valid host name or ip address.');
    return false;
  }

  /**
   * Check if it is a unix user name.
   */
  public function unix_username(string $input): bool {
    if (Validation::unix_username($input))
      return true;
    $this->set_message('unix_username', 'The {field} field must contain a valid UNIX username.');
    return false;
  }

  /**
   * Check if it is a port number.
   */
  public function port(string $input): bool {
    if (Validation::port($input))
      return true;
    $this->set_message('port', 'The {field} field must contain a valid port number.');
    return false;
  }

  /**
   * Check if it is an e-mail.
   * The verification method uses the regular expression proposed in the HTML5 specification.
   * https://html.spec.whatwg.org/multipage/input.html#valid-e-mail-address
   */
  public function email(string $input): bool {
    if (Validation::email($input))
      return true;
    $this->set_message('email', 'The {field} field must contain a valid email address.');
    return false;
  }

  /**
   * Check if it is a file (directory) path.
   */
  public function is_path(string $input, $denyLeadingSlash = false): bool {
    if (Validation::is_path($input, filter_var($denyLeadingSlash, FILTER_VALIDATE_BOOLEAN)))
      return true;
    $this->set_message('is_path', 'The {field} field must contain a valid directory path.');
    return false;
  }
}