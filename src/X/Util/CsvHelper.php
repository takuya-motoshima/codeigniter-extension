<?php
/**
 * Csv helper class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Util;
use \X\Util\Logger;

final class CsvHelper {

  /**
   * Put a line in csv
   *
   * @param string $path
   * @param  array $line
   * @return void
   */
  public static function putRow(string $path, array $line) {
    if (empty($line)) {
        return;
    }
    $fp = fopen($path, 'a');
    if (!flock($fp, LOCK_EX)) {
      throw new \RuntimeException('Unable to get file lock. path=' . $path);
    }
    fputcsv($fp, $line);
    flock($fp, LOCK_UN);
    fclose($fp);
  }

  /**
   * Read csv
   *
   * @return string $path
   * @return callable $callback
   * @return array
   */
  public static function read(string $path, callable $callback = null) {
    if (!file_exists($path)) {
      return null;
    }
    $file = new \SplFileObject($path);
    $file->setFlags(
      \SplFileObject::READ_CSV |
      \SplFileObject::READ_AHEAD |
      \SplFileObject::SKIP_EMPTY |
      \SplFileObject::DROP_NEW_LINE
    );
    $lines = [];
    foreach ($file as $line) {
      if(is_null($line[0])) {
        break;
      }
      if (is_callable($callback)) {
        $line = $callback($line);
      }
      if (!empty($line)) {
        $lines[] = $line;
      }
    }
    return !empty($lines) ? $lines : null;
  }
}