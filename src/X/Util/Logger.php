<?php
/**
 * Log util class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Util;
final class Logger
{

  /**
   * Print debug log
   *
   * @param mixed[] $params
   * @return void
   */
  public static function d(...$params)
  {
    log_message('debug', self::get_log_string($params, debug_backtrace()));
  }

  /**
   * Print info log
   *
   * @param mixed[] $params
   * @return void
   */
  public static function i(...$params)
  {
    log_message('info', self::get_log_string($params, debug_backtrace()));
  }


  /**
   * Print error log
   *
   * @param mixed[] $params
   * @return void
   */
  public static function e(...$params)
  {
    if ($params[0] instanceof \Exception) {
      log_message('error', $params[0]->getMessage() . PHP_EOL . $params[0]->getTraceAsString());
    } else {
      log_message('error', self::get_log_string($params, debug_backtrace()));
    }
  }

  /**
   * Display log
   *
   * @param mixed[] $params
   * @return void
   */
  public static function s(...$params)
  {
    $message = '';
    foreach ($params as $param) {
      if (is_array($param) || is_object($param)) {
        $message .= '<pre>' . htmlspecialchars(print_r($param, true), ENT_QUOTES) . '</pre>';
      } else {
        $message .= htmlspecialchars($param, ENT_QUOTES);
      }
    }
    echo '<div style="border-bottom:1px solid #efefef;padding:4px;">' . $message . '</div>';
  }


  /**
   * Get log string
   *
   * @param  array $params
   * @param  array $trace
   * @return string
   */
  private static function get_log_string(array $params, array $trace):string
  {
    $app_dir = realpath(\FCPATH . '../') . '/';
    $message = str_replace($app_dir, '', $trace[0]['file']) . '(' . $trace[0]['line'] . ')';
    if (isset($trace[1]['class'])) {
      $message .= ' ' . $trace[1]['class'] . '.' . $trace[1]['function'];
    } else if (isset($trace[1]['function'])) {
      $message .= ' ' . $trace[1]['function'];
    }
    if (!empty($params)) {
      $message .= ': ';
      foreach ($params as $param) {
        if (is_array($param) || is_object($param)) {
          $message .= print_r($param, true);
        } else {
          $message .= $param;
        }
      }
    }
    return $message;
  }
}