<?php
/**
 * Log util class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Util;
final class Logger {


  /**
   * Print debug log
   *
   * @param mixed[] $params
   * @return void
   */
  public static function debug(...$params) {
    $message = self::create($params, debug_backtrace());
    $pid = getmypid();
    log_message('debug', "#$pid $message");
  }

  /**
   * Print info log
   *
   * @param mixed[] $params
   * @return void
   */
  public static function info(...$params) {
    $message = self::create($params, debug_backtrace());
    $pid = getmypid();
    log_message('info', "#$pid $message");
  }

  /**
   * Print error log
   *
   * @param mixed[] $params
   * @return void
   */
  public static function error(...$params) {
    $message = $params[0] instanceof \Exception
      ? $params[0]->getMessage() . PHP_EOL . $params[0]->getTraceAsString()
      : self::create($params, debug_backtrace());
    $pid = getmypid();
    log_message('error', "#$pid $message");
  }

  /**
   * Display log in browse or console
   *
   * @param mixed[] $params
   * @return void
   */
  public static function print(...$params) {
    if (!is_cli()) {
      $message = self::create($params, null, false, false, true);
      echo '<div style="border-bottom:1px solid #efefef;padding:4px;">' . $message . '</div>';
    } else {
      $message = self::create($params, debug_backtrace());
      echo $message . PHP_EOL;
      self::debug($message);
    }
  }

  /**
   * Display log without file path.
   *
   * @param mixed[] $params
   * @return void
   */
  public static function printWithoutPath(...$params) {
    if (!is_cli()) {
      $message = self::create($params, null, false, false, true);
      echo '<div style="border-bottom:1px solid #efefef;padding:4px;">' . $message . '</div>';
    } else {
      $message = self::create($params, debug_backtrace(), false);
      echo $message . PHP_EOL;
      self::debug($message);
    }
  }

  /**
   * Create log string
   *
   * @param  array $params
   * @param  array $trace
   * @param  bool  $showPath
   * @param  bool  $showFunction
   * @param  bool  $isBrowser
   * @return string
   */
  private static function create(
    array $params,
    ?array $trace,
    bool $showPath = true,
    bool $showFunction = false,
    bool $isBrowser = false
  ): string {
    $message = '';
    if ($showPath && !empty($trace)) {
      $message = str_replace(realpath(\FCPATH . '../') . '/', '', $trace[0]['file']) . '(' . $trace[0]['line'] . ')';
      if ($showFunction) {
        if (isset($trace[1]['class'])) $message .= ' ' . $trace[1]['class'] . '.' . $trace[1]['function'];
        else if (isset($trace[1]['function'])) $message .= ' ' . $trace[1]['function'];
      }
      $message .= ':';
    } else if (is_cli()) {
      $message .= date('Y-m-d H:i:s') . ' --> ';
    }
    foreach ($params as $param) {
      if (is_array($param) || is_object($param)) {
        $message .= $isBrowser ? '<pre>' . htmlspecialchars(print_r($param, true), ENT_QUOTES) . '</pre>' : print_r($param, true);
      } else {
        $message .= $isBrowser ? htmlspecialchars($param, ENT_QUOTES) : $param;
      }
    }
    return $message;
  }
}