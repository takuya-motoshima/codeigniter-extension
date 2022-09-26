<?php
namespace X\Library;
use \X\Util\Logger;

abstract class Input extends \CI_Input {
  /**
   * Fetch an item from the PUT array.
   */
  public function put($index = NULL, $xss_clean = NULL) {
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
      if (strpos($part, 'application/octet-stream') !== FALSE) {
        preg_match('/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s', $part, $matches);
        $name = $matches[1];
        $value = $matches[2] ?? null;
        $data['files'][$name] = $value;
      } else {
        preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $part, $matches);
        $name = $matches[1];
        $value = $matches[2] ?? null;
        if ($this->isNestedNode($name, $parentName, $childNames))
          $this->setNestedNode($data, $value, $parentName, $childNames);
        else
          $data[$name] = $value;
      }
    }
    return empty($index) ? $data : $data[$index] ?? '';
  }

  /**
   * Fetch an item from the DELETE array.
   */
  public function delete($index = NULL, $xss_clean = NULL) {
    return parent::input_stream($index, $xss_clean);
  }

  /**
   * Is nested node.
   */
  private function isNestedNode(string $name, ?string &$parentName = null, ?string &$childNames = null): bool {
    if (!preg_match('/^([a-z0-9\-_:\.]+)(\[..*)$/i', $name, $matches))
      return false;
    $parentName = $matches[1];
    $childNames = $matches[2];
    return true;
  }

  /**
   * Set nested node.
   */
  private function setNestedNode(array &$data, ?string $value, string $parentName, string $childNames) {
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