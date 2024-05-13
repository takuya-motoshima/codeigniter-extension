<?php
namespace X\Util;

/**
 * Validator Utility.
 */
final class Validation {
  /**
   * Check if it is a host name.
   * @param string $value Value to be validated.
   * @return bool Pass is true, fail is false.
   */
  public static function hostname(string $value): bool {
    return preg_match('/^(?!:\/\/)([a-zA-Z0-9-_]+\.)*[a-zA-Z0-9][a-zA-Z0-9-_]+\.[a-zA-Z]{2,11}?$/', $value) === 1
      || $value === 'localhost';
  }

  /**
   * Check if it is an IP.
   * @param string $value Value to be validated.
   * @return bool Pass is true, fail is false.
   */
  public static function ipaddress(string $value): bool {
    return preg_match('/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/', $value) === 1;
  }

  /**
   * Check if it is IP or CIDR format.
   * @param string $value Value to be validated.
   * @return bool Pass is true, fail is false.
   */
  public static function ipaddress_or_cidr(string $value): bool {
    return preg_match('/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])(\/(\d|[1-2]\d|3[0-2]))?$/', $value) === 1;
  }

  /**
   * Check if it is a host name or IP.
   * @param string $value Value to be validated.
   * @return bool Pass is true, fail is false.
   */
  public static function hostname_or_ipaddress(string $value): bool {
    return preg_match('/^((([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])|((?!:\/\/)([a-zA-Z0-9-_]+\.)*[a-zA-Z0-9][a-zA-Z0-9-_]+\.[a-zA-Z]{2,11}?))$/', $value) === 1
      || $value === 'localhost';
  }

  /**
   * Check if it is a unix user name.
   * @param string $value Value to be validated.
   * @return bool Pass is true, fail is false.
   */
  public static function unix_username(string $value): bool {
    return preg_match('/^[a-z_]([a-z0-9_-]{0,31}|[a-z0-9_-]{0,30}\$)$/', $value) === 1;
  }

  /**
   * Check if it is a port number.
   * @param string $value Value to be validated.
   * @return bool Pass is true, fail is false.
   */
  public static function port(string $value): bool {
    return preg_match('/^\d+$/', $value) && (int) $value >= 0 && (int) $value <= 65535;
  }

  /**
   * Check if it is an e-mail.
   * @param string $value Value to be validated.
   * @return bool Pass is true, fail is false.
   */
  public static function email(string $value): bool {
    // NOTE: Changed to the regular expression used for email address validation in the Form Validation JS library(https://formvalidation.io/guide/validators/email-address/).
    return preg_match('/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/', $value) === 1;
    // return preg_match("/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/", $value) === 1;
  }

  /**
   * Check if it is a file (directory) path.
   * @param string $value Value to be validated.
   * @return bool Pass is true, fail is false.
   */
  public static function is_path(string $value, bool $denyLeadingSlash=false): bool {
    // UNIX path regular expression.
    // Based on the "/^(\/|(\/[\w\s@^!#$%&-]+)+(\.[a-z]+\/?)?)$/i" regular expression, the leading and trailing slashes have been improved to be optional.
    $re = "/^(\/|(\/?[\w\s@^!#$%&-\.]+)+\/?)$/";

    // Validate input values.
    $valid = preg_match($re, $value) === 1;

    // If leading slashes are allowed, return the result immediately.
    if (!$denyLeadingSlash)
      return $valid;

    // If leading slashes are not allowed, an error is returned if there is a leading slash.
    return $valid && preg_match('/^\//', $value) === 0;
  }
}