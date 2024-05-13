<?php
namespace X\Util;
use \X\Util\Logger;

/**
 * CSV Utility.
 */
final class CsvHelper {
  /**
   * Put row.
   * @param string $filePath CSV file path.
   * @param array $row An array of fields.
   * @return void
   */
  public static function putRow(string $filePath, array $row): void {
    if (empty($row))
      return;
    $fp = fopen($filePath, 'a');
    if (!flock($fp, LOCK_EX))
      throw new \RuntimeException('Unable to get file lock. path=' . $filePath);
    fputcsv($fp, $row);
    flock($fp, LOCK_UN);
    fclose($fp);
  }

  /**
   * Read the CSV.
   * @param string $filePath CSV file path.
   * @param callable|null $callback Receives the rows to be registered in the result set and modifies the rows if necessary.
   * @return array|null List of rows.
   */
  public static function read(string $filePath, callable $callback=null) {
    if (!file_exists($filePath))
      return null;
    $file = new \SplFileObject($filePath);
    $file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE);
    $rows = [];
    foreach ($file as $row) {
      if (is_null($row[0]))
        break;
      if (is_callable($callback))
        $row = $callback($row);
      if (!empty($row))
        $rows[] = $row;
    }
    return !empty($rows) ? $rows : null;
  }
}