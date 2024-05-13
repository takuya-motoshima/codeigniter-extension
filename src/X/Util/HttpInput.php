<?php
namespace X\Util;
use \X\Util\Logger;

/**
 * Assists in processing input data for requests.
 */
final class HttpInput {
  /**
   * Fetch an item from the PUT array.
   * @param mixed|null $index (optional) Index for item to be fetched from $array. Default is null.
   * @param bool|null $xssClean (optional) Whether to apply XSS filtering. Default is null.
   * @return mixed PUT data.
   */
  public static function put($index=null, $xssClean=null) {
    $data = file_get_contents('php://input');
    preg_match('/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);
    if (!count($matches)) {
      if ($_SERVER['CONTENT_TYPE'] != 'application/json')
        parse_str($data, $data);
      return empty($index) ? $data : $data[$index] ?? '';
    }
    $boundary = $matches[1];
    $parts = preg_split('/-+' . $boundary . '/', $data);
    array_pop($parts);
    $data = [];
    foreach ($parts as $part) {
      if (empty($part))
        continue;
      if (strpos($part, 'application/octet-stream') !== false) {
        preg_match('/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s', $part, $matches);
        $name = $matches[1];
        $value = $matches[2] ?? null;
        $data['files'][$name] = $value;
      } else {
        preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $part, $matches);
        $name = $matches[1];
        $value = $matches[2] ?? null;
        if (self::isNestedNode($name, $parentName, $childNames))
          self::setNestedNode($data, $value, $parentName, $childNames);
        else
          $data[$name] = $value;
      }
    }
    return empty($index) ? $data : $data[$index] ?? '';
  }

  /**
   * Check for nested nodes.
   * @param string $name Name attribute.
   * @param string|null $parentName (optional) Parent node name.
   * @param string|null $childNames (optional) Child node name.
   * @return bool Nested node or not.
   */
  private static function isNestedNode(string $name, ?string &$parentName=null, ?string &$childNames=null): bool {
    if (!preg_match('/^([a-z0-9\-_:\.]+)(\[..*)$/i', $name, $matches))
      return false;
    $parentName = $matches[1];
    $childNames = $matches[2];
    return true;
  }

  /**
   * Set nested node.
   * @param array $data Request Data.
   * @param string|null $value Parameter Value.
   * @param string $parentName Parent node name.
   * @param string $childNames Child node name.
   * @return void
   */
  private static function setNestedNode(array &$data, ?string $value, string $parentName, string $childNames): void {
    preg_match_all('/\[([a-z0-9\-_:\.]*)\]/i', $childNames, $matches);
    $names = $matches[1];
    array_unshift($names, $parentName);
    $ref = &$data;
    while(($name = array_shift($names)) !== null) {
      if (!empty($name) && !array_key_exists($name, $ref ?? []))
        $ref[$name] = [];
      if (count($names) > 0) {
        $ref = &$ref[$name];
        continue;
      }
      if (!empty($name) || $name === 0 || $name === '0')
        $ref[$name] = $value;
      else
        $ref[] = $value;
      break;
    }
  }
}