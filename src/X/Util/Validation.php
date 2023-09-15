<?php
namespace X\Util;
use \X\Util\Logger;

final class Validation {
  /**
   * Check if it is a host name.
   */
  public static function hostname(string $input): bool {
    return preg_match('/^(?!:\/\/)([a-zA-Z0-9-_]+\.)*[a-zA-Z0-9][a-zA-Z0-9-_]+\.[a-zA-Z]{2,11}?$/', $input) === 1
      || $input === 'localhost';
  }

  /**
   * Check if it is an IP.
   */
  public static function ipaddress(string $input): bool {
    return preg_match('/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/', $input) === 1;
  }

  /**
   * Check if it is IP or CIDR format.
   */
  public static function ipaddress_or_cidr(string $input): bool {
    return preg_match('/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])(\/(\d|[1-2]\d|3[0-2]))?$/', $input) === 1;
  }

  /**
   * Check if it is a host name or IP.
   */
  public static function hostname_or_ipaddress(string $input): bool {
    return preg_match('/^((([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])|((?!:\/\/)([a-zA-Z0-9-_]+\.)*[a-zA-Z0-9][a-zA-Z0-9-_]+\.[a-zA-Z]{2,11}?))$/', $input) === 1
      || $input === 'localhost';
  }

  /**
   * Check if it is a unix user name.
   */
  public static function unix_username(string $input): bool {
    return preg_match('/^[a-z_]([a-z0-9_-]{0,31}|[a-z0-9_-]{0,30}\$)$/', $input) === 1;
  }

  /**
   * Check if it is a port number.
   */
  public static function port(string $input): bool {
    return preg_match('/^\d+$/', $input) && (int) $input >= 0 && (int) $input <= 65535;
  }

  /**
   * Check if it is an e-mail.
   */
  public static function email(string $input): bool {
    // NOTE: Changed to the regular expression used for email address validation in the Form Validation JS library(https://formvalidation.io/guide/validators/email-address/).
    return preg_match('/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/', $input) === 1;
    // return preg_match("/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/", $input) === 1;
  }

  /**
   * Check if it is a file (directory) path.
   */
  public static function is_path(string $input): bool {
    // UNIX path regular expression.
    // Based on the "/^(\/|(\/[\w\s@^!#$%&-]+)+(\.[a-z]+\/?)?)$/i" regular expression, the leading and trailing slashes have been improved to be optional.
    $re = "/^(\/|(\/?[\w\s@^!#$%&-\.]+)+\/?)$/";
    return preg_match($re, $input) === 1;
  }
}