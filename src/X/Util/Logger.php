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
      self::d($message);
    }
  }

  /**
   * Create log string
   *
   * @param  array $params
   * @param  array $trace
   * @return string
   */
  private static function createLogString(array $params, array $trace): string {
    $message = str_replace(realpath(\FCPATH . '../') . '/', '', $trace[0]['file']) . '(' . $trace[0]['line'] . ')';
    if (isset($trace[1]['class'])) {
      $message .= ' ' . $trace[1]['class'] . '.' . $trace[1]['function'];
    } else if (isset($trace[1]['function'])) {
      $message .= ' ' . $trace[1]['function'];
    }
    if (!empty($params)) {
      $message .= ':';
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

  /**
   * @deprecated 3.2.0 No longer used by internal code and not recommended.
   * @see Logger::debug()
   */
  public static function d(...$params) {
    log_message('debug', self::createLogString($params, debug_backtrace()));
  }

  /**
   * @deprecated 3.2.0 No longer used by internal code and not recommended.
   * @see Logger::info()
   */
  public static function i(...$params) {
    log_message('info', self::createLogString($params, debug_backtrace()));
  }

  /**
   * @deprecated 3.2.0 No longer used by internal code and not recommended.
   * @see Logger::error()
   */
  public static function e(...$params) {
    if ($params[0] instanceof \Exception) {
      log_message('error', $params[0]->getMessage() . PHP_EOL . $params[0]->getTraceAsString());
    } else {
      log_message('error', self::createLogString($params, debug_backtrace()));
    }
  }

  /**
   * @deprecated 3.2.0 No longer used by internal code and not recommended.
   * @see Logger::print()
   */
  public static function s(...$params) {
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
   * @deprecated 3.2.0 No longer used by internal code and not recommended.
   * @see Logger::print()
   */
  public static function c(...$params) {
    $message = self::createLogString($params, debug_backtrace());
    echo $message . PHP_EOL;
    self::d($message);
  }
}