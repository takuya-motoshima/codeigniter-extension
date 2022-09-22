<?php
namespace X\Util;
use \X\Util\Logger;

final class Validation {
  /**
   * Validate hostname.
   */
  public static function hostname(string $input): bool {
    return preg_match('/^(?!:\/\/)([a-zA-Z0-9-_]+\.)*[a-zA-Z0-9][a-zA-Z0-9-_]+\.[a-zA-Z]{2,11}?$/', $input) === 1
      || $input === 'localhost';
  }

  /**
   * Validate ip address.
   */
  public static function ipaddress(string $input): bool {
    return preg_match('/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/', $input) === 1;
  }

  /**
   * Validate ip address or CIDR.
   */
  public static function ipaddress_or_cidr(string $input): bool {
    return preg_match('/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])(\/(\d|[1-2]\d|3[0-2]))?$/', $input) === 1;
  }

  /**
   * Validate hostname or ip address.
   */
  public static function hostname_or_ipaddress(string $input): bool {
    return preg_match('/^((([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])|((?!:\/\/)([a-zA-Z0-9-_]+\.)*[a-zA-Z0-9][a-zA-Z0-9-_]+\.[a-zA-Z]{2,11}?))$/', $input) === 1
      || $input === 'localhost';
  }

  /**
   * Validate UNIX username.
   */
  public static function unix_username(string $input): bool {
    return preg_match('/^[a-z_]([a-z0-9_-]{0,31}|[a-z0-9_-]{0,30}\$)$/', $input) === 1;
  }

  /**
   * Validate port number.
   */
  public static function port(string $input): bool {
    return preg_match('/^\d+$/', $input) && (int) $input >= 0 && (int) $input <= 65535;
  }

  /**
   * Validate email.
   * The verification method uses the regular expression proposed in the HTML5 specification.
   * https://html.spec.whatwg.org/multipage/input.html#valid-e-mail-address
   */
  public static function email(string $input): bool {
    return preg_match("/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/", $input) === 1;
  }

  /**
   * Validate directory path.
   */
  public static function directory_path(string $input): bool {
    return preg_match("/^\/$|(\/[a-zA-Z_0-9-]+)+$/", $input) === 1;
  }
}