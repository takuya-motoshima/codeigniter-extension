<?php
/**
 * Input Class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2018 Takuya Motoshima
 */
namespace X\Library;
abstract class Input extends \CI_Input
{

  /**
   * Fetch an item from the PUT array
   *
   * @param   mixed   $index      Index for item to be fetched from $_PUT
   * @param   bool    $xss_clean  Whether to apply XSS filtering
   * @return  mixed
   */
  public function put($index = NULL, $xss_clean = NULL)
  {

    // read incoming data
    $input = file_get_contents('php://input');

    // grab multipart boundary from content type header
    preg_match('/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);

    // content type is probably regular form-encoded
    if (!count($matches)) {
      // we expect regular puts to containt a query string containing data
      parse_str(urldecode($input), $input);
      return $input;
    }
    $boundary = $matches[1];

    // split content by boundary and get rid of last -- element
    $blocks = preg_split('/-+' . $boundary . '/', $input);
    array_pop($blocks);

    // loop data blocks
    $input = [];
    foreach ($blocks as $id => $block) {
      if (empty($block)) {
        continue;
      }

      // you'll have to var_dump $block to understand this and maybe replace \n or \r with a visibile char
      // parse uploaded files
      if (strpos($block, 'application/octet-stream') !== FALSE) {
        // match 'name', then everything after 'stream' (optional) except for prepending newlines
        preg_match('/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s', $block, $matches);
        $input['files'][$matches[1]] = $matches[2];
      } else {
        // parse all other fields
        // match "name" and optional value in between newline sequences
        preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $block, $matches);
        $nameAttr = $matches[1];
        $valueAttr = $matches[2];
        if ($this->isNestedFormItem($nameAttr, $rootKey, $childKeys)) {
          $this->setNestedFormItem($input, $rootKey, $childKeys);
        } else {
          $input[$nameAttr] = $valueAttr;
        }
      }
    }
    return $input;
    // return parent::input_stream($index, $xss_clean);
  }

  /**
   * Fetch an item from the DELETE array
   *
   * @param   mixed   $index      Index for item to be fetched from $_DELETE
   * @param   bool    $xss_clean  Whether to apply XSS filtering
   * @return  mixed
   */
  public function delete($index = NULL, $xss_clean = NULL)
  {
    return parent::input_stream($index, $xss_clean);
  }


  private function isNestedFormItem(
    string $nameAttr, 
    ?string &$rootKey = null, 
    ?string &$childKeys = null
  ): bool
  {
    if (!preg_match('/^([a-z0-9\-_:\.]+)(\[..*)$/i', $nameAttr, $matches)) {
      return false;
    }
    $rootKey = $matches[1];
    $childKeys = $matches[2];
    return true;
  }

  private function setNestedFormItem(
    array &$input, 
    string $rootKey, 
    string $childKeys
  )
  {
    preg_match_all('/\[([a-z0-9\-_:\.]*)\]/i', $childKeys, $matches);
    $keys = $matches[1];
    array_unshift($keys, $rootKey);
    $tmp = &$input;
    while(($key = array_shift($keys)) !== null) {
      if (!array_key_exists($key, $tmp)) {
        $tmp[$key] = [];
      }
      if (count($keys) > 0) {
        $tmp = &$tmp[$key];
      } else {
        $tmp[$key] = true;
      }
    }
  }
}