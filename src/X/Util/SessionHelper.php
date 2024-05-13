<?php
namespace X\Util;
use \X\Util\Logger;

/**
 * Session Helper.
 */
final class SessionHelper {
  /**
   * Unserialize the session.
   * @param string $session Serialized session data.
   * @return array Unserialized session data.
   */
  public static function unserialize(string $session) {
    $unserialized = [];
    $params = preg_split('/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\|/', $session, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    for($i=0; $params[$i]; $i++)
      $unserialized[$params[$i++]] = unserialize($params[$i]);
    return $unserialized;
  }

  /**
   * Unserialize the session. When session.serialize_handler is "php"
   * @param string $session Serialized session data.
   * @return array Unserialized session data.
   */
  public static function unserializePhp(string $session): array {
    $unserialized = [];
    $offset = 0;
    while ($offset < strlen($session)) {
      if (!strstr(substr($session, $offset), '|'))
        throw new \RuntimeException('invalid data, remaining: ' . substr($session, $offset));
      $pos = strpos($session, '|', $offset);
      $num = $pos - $offset;
      $varName = substr($session, $offset, $num);
      $offset += $num + 1;
      $data = unserialize(substr($session, $offset));
      $unserialized[$varName] = $data;
      $offset += strlen(serialize($data));
    }
    return $unserialized;
  }

  /**
   * Unserialize the session. When session.serialize_handler is "php_binary".
   * @param string $session Serialized session data.
   * @return array Unserialized session data.
   */
  public static function unserializePhpBinary(string $session): array {
    $unserialized = [];
    $offset = 0;
    while ($offset < strlen($session)) {
      $num = ord($session[$offset]);
      $offset += 1;
      $varName = substr($session, $offset, $num);
      $offset += $num;
      $data = unserialize(substr($session, $offset));
      $unserialized[$varName] = $data;
      $offset += strlen(serialize($data));
    }
    return $unserialized;
  }
}