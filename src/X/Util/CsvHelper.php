<?php
namespace X\Util;
use \X\Util\Logger;

final class CsvHelper {
  /**
   * Put a line in csv.
   */
  public static function putRow(string $inputPath, array $line) {
    if (empty($line))
      return;
    $fp = fopen($inputPath, 'a');
    if (!flock($fp, LOCK_EX))
      throw new \RuntimeException('Unable to get file lock. path=' . $inputPath);
    fputcsv($fp, $line);
    flock($fp, LOCK_UN);
    fclose($fp);
  }

  /**
   * Read csv.
   */
  public static function read(string $inputPath, callable $callback = null) {
    if (!file_exists($inputPath))
      return null;
    $file = new \SplFileObject($inputPath);
    $file->setFlags(
      \SplFileObject::READ_CSV |
      \SplFileObject::READ_AHEAD |
      \SplFileObject::SKIP_EMPTY |
      \SplFileObject::DROP_NEW_LINE
    );
    $lines = [];
    foreach ($file as $line) {
      if (is_null($line[0]))
        break;
      if (is_callable($callback))
        $line = $callback($line);
      if (!empty($line))
        $lines[] = $line;
    }
    return !empty($lines) ? $lines : null;
  }
}