<?php

use \X\Util\Logger;
use \X\Util\Image;

/**
 * File util class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Util;
final class FileUtil
{

  /**
   * 
   * Make directory
   *
   * @throws RuntimeException
   * @param string $dirPath
   * @param int $mode
   * @return void
   */
  public static function make_direcoty(string $dirPath, int $mode = 0755)
  {
    if (file_exists($dirPath)) {
      return;
    }
    if (mkdir($dirPath, $mode, true) === false) {
      throw new \RuntimeException('Cant create directory ' . $dirPath);
    }
  }

  /**
   * 
   * Rename file
   * 
   * @throws RuntimeException
   * @param string $oldFilePath
   * @param string $newFilePath
   * @return void
   */
  public static function rename_file(string $oldFilePath, string $newFilePath)
  {
    if (!file_exists($oldFilePath)) {
      throw new \RuntimeException('Not found file ' . $oldFilePath);
    }
    self::make_direcoty(dirname($newFilePath));
    if (rename($oldFilePath, $newFilePath) === false) {
      throw new \RuntimeException('Can not rename from ' . $oldFilePath . ' to ' . $newFilePath);
    }
  }

  /**
   * 
   * Copy file
   * 
   * @throws RuntimeException
   * @param string $srcFilePath
   * @param string $dstFilePath
   * @return void
   */
  public static function copy_file(string $srcFilePath, string $dstFilePath)
  {
    if (!file_exists($srcFilePath)) {
      throw new \RuntimeException('Not found file ' . $srcFilePath);
    } else if (!is_file($srcFilePath)) {
      throw new \RuntimeException($srcFilePath . ' is not file');
    }
    self::make_direcoty(dirname($dstFilePath));
    if (copy($srcFilePath, $dstFilePath) === false) {
      throw new \RuntimeException('Can not copy from ' . $srcFilePath . ' to ' . $dstFilePath);
    }
  }

  /**
   * 
   * Copy directory
   *
   * @throws RuntimeException
   * @param string $srcDirPath
   * @param string $dstDirPath
   * @return void
   */
  public static function copy_directory(string $srcDirPath, string $dstDirPath)
  {
    if (!file_exists($srcDirPath)) {
      throw new \RuntimeException('Not found directory ' . $srcDirPath);
    } else if (!is_dir($srcDirPath)) {
      throw new \RuntimeException($srcDirPath . ' is not directory');
    }
    self::make_direcoty($dstDirPath);
    $iterator = new \RecursiveIteratorIterator(
      new \RecursiveDirectoryIterator($srcDirPath, \RecursiveDirectoryIterator::SKIP_DOTS),
      \RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($iterator as $file) {
      if ($file->isDir()) {
        self::make_direcoty($dstDirPath . '/' . $iterator->getSubPathName());
      } else {
        self::copy_file($file, $dstDirPath . '/' . $iterator->getSubPathName());
      }
    }
  }

  /**
   * 
   * Delete directory or file
   *
   * @param string[] $paths
   * @param bool $isRemoveRootDir
   */
  public static function delete(...$paths)
  // public static function delete(string ...$paths)
  {
    $isRemoveRootDir = true;
    if (is_bool(end($paths))) {
      $isRemoveRootDir = end($paths);
      unset($paths[count($paths) - 1]);
    }
    foreach ($paths as $path) {
      if (is_file($path)) {
        unlink($path);
        continue;
      }
      $iterator = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
        \RecursiveIteratorIterator::CHILD_FIRST
      );
      foreach ($iterator as $file) {
        if ($file->isDir()) {
          rmdir($file);
        } else {
          unlink($file);
        }
      }
      if ($isRemoveRootDir) {
        rmdir($path);
      }
    }
  }

  /**
   * 
   * Replace file content
   *
   * @param string $path
   * @return  void
   */
  public static function replace(string $path, array $replace)
  {
    $content = file_get_contents($path);
    $content = str_replace(array_keys($replace), array_values($replace), $content);
    file_put_contents($path, $content);
  }

  /**
   * 
   * @deprecated Please use Image::copy
   */
  public static function put_image(string $orgFilePath, string $dirPath, string $replacementFileName = null): string
  {
    return Image::copy($orgFilePath, $dirPath, $replacementFileName);
  }

  /**
   *
   * @deprecated Please use Image::putBase64
   */
  public static function put_base64_image(string $imageBase64, string $dirPath, string $fileName): array
  {
    return Image::putBase64($imageBase64, $dirPath, $fileName);
  }

  /**
   *
   * @deprecated Please use Image::read
   */
  public static function read_image(string $imagePath): string
  {
    return Image::read($imagePath);
  }

  /**
   * Put a line in csv
   *
   * @param string $path
   * @param  array $line
   * @return void
   */
  public static function put_csv(string $path, array $line)
  {
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
   * Read csv file
   *
   * @return string $path
   * @return callable $callback
   * @return array
   */
  public static function read_csv(string $path, callable $callback = null)
  {
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


  /**
   * 
   * Find file
   * 
   * @param  string $pattern
   * @return array
   */
  public static function find(string $pattern): array
  {
    $files = [];
    foreach (glob($pattern) as $file) {
      $files[] = basename($file);
    }
    return $files;
  }
}