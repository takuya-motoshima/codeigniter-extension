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
    log_message('debug', self::createLogString($params, debug_backtrace()));
  }

  /**
   * Print info log
   *
   * @param mixed[] $params
   * @return void
   */
  public static function info(...$params) {
    log_message('info', self::createLogString($params, debug_backtrace()));
  }

  /**
   * Print error log
   *
   * @param mixed[] $params
   * @return void
   */
  public static function error(...$params) {
    if ($params[0] instanceof \Exception) {
      log_message('error', $params[0]->getMessage() . PHP_EOL . $params[0]->getTraceAsString());
    } else {
      log_message('error', self::createLogString($params, debug_backtrace()));
    }
  }

  /**
   * Display log in browse or console
   *
   * @param mixed[] $params
   * @return void
   */
  public static function print(...$params) {
    if (!is_cli()) {
      $message = '';
      foreach ($params as $param) {
        if (is_array($param) || is_object($param)) {
          $message .= '<pre>' . htmlspecialchars(print_r($param, true), ENT_QUOTES) . '</pre>';
        } else {
          $message .= htmlspecialchars($param, ENT_QUOTES);
        }
      }
      echo '<div style="border-bottom:1px solid #efefef;padding:4px;">' . $message . '</div>';
    } else {
      $message = self::createLogString($params, debug_backtrace());
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
      $message = '';
      foreach ($params as $param) {
        if (is_array($param) || is_object($param)) {
          $message .= '<pre>' . htmlspecialchars(print_r($param, true), ENT_QUOTES) . '</pre>';
        } else {
          $message .= htmlspecialchars($param, ENT_QUOTES);
        }
      }
      echo '<div style="border-bottom:1px solid #efefef;padding:4px;">' . $message . '</div>';
    } else {
      $message = self::createLogString($params, debug_backtrace(), false);
      echo $message . PHP_EOL;
      self::debug($message);
    }
  }

  /**
   * Create log string
   *
   * @param  array $params
   * @param  array $trace
   * @param  bool  $witPath
   * @param  bool  $showFunctionName
   * @return string
   */
  private static function createLogString(
    array $params,
    array $trace,
    bool $witPath = true,
    bool $showFunction = false
  ): string {
    $message = '';
    if ($witPath) {
      $message = str_replace(realpath(\FCPATH . '../') . '/', '', $trace[0]['file']) . '(' . $trace[0]['line'] . ')';
      if ($showFunction) {
        if (isset($trace[1]['class'])) $message .= ' ' . $trace[1]['class'] . '.' . $trace[1]['function'];
        else if (isset($trace[1]['function'])) $message .= ' ' . $trace[1]['function'];
      }
    } else if (is_cli()) {
      $message .= date('Y-m-d H:i:s') . ' --> ';
    }
    if (!empty($params)) {
      if ($witPath) $message .= ':';
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