<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \X\Util\Logger;
use \X\Util\StringHelper;

class StringHelperTest extends AppController {
  public function ellipsis() {
    // Omit long strings.
    $str = 'This is a long string.';
    $str = StringHelper::ellipsis($str, 10);
    Logger::print('$str=', $str); 

    // Omit long strings containing Unicode.
    $str = 'ユニコードを含む長い文字列を省略する';
    $str = StringHelper::ellipsis($str, 10);
    Logger::print('$str=', $str); 
  }
}