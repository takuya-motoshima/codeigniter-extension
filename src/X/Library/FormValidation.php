<?php
namespace X\Library;
use \X\Util\Validation;

/**
 * CI_Form_validation extension.
 */
abstract class FormValidation extends \CI_Form_validation {
  /**
   * Initialize FormValidation.
   * @param array $rules (optional) Validation Rules.
   */
  function __construct($rules=[]) {
    parent::__construct($rules);
  }

  /**
  * Check if it is the date and time.
  * ```php
  * $this->form_validation
  *   ->set_data(['datetime' => '2021-02-03 17:46:00'])
  *   ->set_rules('datetime', 'datetime', 'required|datetime[Y-m-d H:i:s]');
  * ```
   * @param string $value Input value.
   * @param string $format Date Format.
   * @return bool Check Results.
  */
  public function datetime(string $value, string $format): bool {
    $value = str_replace(['-', '/'], '-', $value);
    if (date($format, strtotime($value)) == $value)
      return true;
    $this->set_message('datetime', "The {field} field must have format $format.");
    return false;
  }

  /**
   * Check if it is a host name.
   * @param string $value Input value.
   * @return bool Check Results.
   */
  public function hostname(string $value): bool {
    if (Validation::hostname($value))
      return true;
    $this->set_message('hostname', 'The {field} field must contain a valid host name.');
    return false;
  }

  /**
   * Check if it is an IP.
   * @param string $value Input value.
   * @return bool Check Results.
   */
  public function ipaddress(string $value): bool {
    if (Validation::ipaddress($value))
      return true;
    $this->set_message('ipaddress', 'The {field} field must contain a valid ip address.');
    return false;
  }

  /**
   * Check if it is IP or CIDR format.
   * @param string $value Input value.
   * @return bool Check Results.
   */
  public function ipaddress_or_cidr(string $value): bool {
    if (Validation::ipaddress_or_cidr($value))
      return true;
    $this->set_message('ipaddress_or_cidr', 'The {field} field must contain a valid ip address or CIDR.');
    return false;
  }

  /**
   * Check if it is a host name or IP.
   * @param string $value Input value.
   * @return bool Check Results.
   */
  public function hostname_or_ipaddress(string $value): bool {
    if (Validation::hostname_or_ipaddress($value))
      return true;
    $this->set_message('hostname_or_ipaddress', 'The {field} field must contain a valid host name or ip address.');
    return false;
  }

  /**
   * Check if it is a unix user name.
   * @param string $value Input value.
   * @return bool Check Results.
   */
  public function unix_username(string $value): bool {
    if (Validation::unix_username($value))
      return true;
    $this->set_message('unix_username', 'The {field} field must contain a valid UNIX username.');
    return false;
  }

  /**
   * Check if it is a port number.
   * @param string $value Input value.
   * @return bool Check Results.
   */
  public function port(string $value): bool {
    if (Validation::port($value))
      return true;
    $this->set_message('port', 'The {field} field must contain a valid port number.');
    return false;
  }

  /**
   * Check if it is an e-mail.
   * The verification method uses the regular expression proposed in the HTML5 specification.
   * https://html.spec.whatwg.org/multipage/input.html#valid-e-mail-address
   * @param string $value Input value.
   * @return bool Check Results.
   */
  public function email(string $value): bool {
    if (Validation::email($value))
      return true;
    $this->set_message('email', 'The {field} field must contain a valid email address.');
    return false;
  }

  /**
   * Check if it is a file (directory) path.
   * @param string $value Input value.
   * @param mixed $denyLeadingSlash Whether leading slashes are allowed or not.
   * @return bool Check Results.
   */
  public function is_path(string $value, $denyLeadingSlash=false): bool {
    if (Validation::is_path($value, filter_var($denyLeadingSlash, FILTER_VALIDATE_BOOLEAN)))
      return true;
    $this->set_message('is_path', 'The {field} field must contain a valid directory path.');
    return false;
  }
}