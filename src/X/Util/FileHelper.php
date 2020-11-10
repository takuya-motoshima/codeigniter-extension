<?php

use \X\Util\Logger;

/**
 * File helper class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Util;
use \X\Util\Logger;
final class FileHelper {

  /**
   * 
   * Make directory
   *
   * @throws RuntimeException
   * @param string $dirPath
   * @param int $mode
   * @return void
   */
  public static function makeDirectory(string $dirPath, int $mode = 0755) {
    try {
      if (file_exists($dirPath)) return;
      if (@mkdir($dirPath, $mode, true) === false) throw new \RuntimeException('Cant create directory ' . $dirPath);
    } catch (\Throwable $e) {
      Logger::info($e->getMessage());
    }
  }

  /**
   * 
   * Move
   * 
   * e.g:
   * // /tmp/old.txt -> /home/new.txt
   * \X\Util\FileHelper::move('/tmp/old.txt', '/home/new.txt');
   * // /tmp/old.txt -> ./tmp/new.txt
   * \X\Util\FileHelper::move('/tmp/old.txt', 'new.txt');
   * // /tmp/old.txt -> ./tmp/new.txt
   * \X\Util\FileHelper::move('/tmp/old.txt', 'new');
   *  
   * @throws RuntimeException
   * @param string $srcFilepath
   * @param string $dstFilepath
   * @return void
   */
  public static function move(string $srcFilepath, string $dstFilepath) {
    if (!file_exists($srcFilepath)) throw new \RuntimeException('Not found file ' . $srcFilepath);
    if (strpos($dstFilepath, '/') === false) {
      if (strpos($dstFilepath, '.') === false) $dstFilepath = $dstFilepath . '.' . pathinfo($srcFilepath, PATHINFO_EXTENSION);
      $dstFilepath = pathinfo($srcFilepath, PATHINFO_DIRNAME) . '/' . $dstFilepath;
    } else {
      self::makeDirectory(dirname($dstFilepath));;
    }
    if (rename($srcFilepath, $dstFilepath) === false) throw new \RuntimeException('Can not rename from ' . $srcFilepath . ' to ' . $dstFilepath);
  }

  /**
   * 
   * Copy file
   * 
   * @throws RuntimeException
   * @param string $srcFilepath
   * @param string $dstFilepath
   * @return void
   */
  public static function copyFile(string $srcFilepath, string $dstFilepath) {
    if (!file_exists($srcFilepath)) throw new \RuntimeException('Not found file ' . $srcFilepath);
    else if (!is_file($srcFilepath)) throw new \RuntimeException($srcFilepath . ' is not file');
    self::makeDirectory(dirname($dstFilepath));
    if (copy($srcFilepath, $dstFilepath) === false) throw new \RuntimeException('Can not copy from ' . $srcFilepath . ' to ' . $dstFilepath);
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
  public static function copyDirectory(string $srcDirPath, string $dstDirPath) {
    if (!file_exists($srcDirPath)) throw new \RuntimeException('Not found directory ' . $srcDirPath);
    else if (!is_dir($srcDirPath)) throw new \RuntimeException($srcDirPath . ' is not directory');
    self::makeDirectory($dstDirPath);
    $dirIt = new \RecursiveDirectoryIterator($srcDirPath, \RecursiveDirectoryIterator::SKIP_DOTS);
    $fileIt = new \RecursiveIteratorIterator($dirIt, \RecursiveIteratorIterator::SELF_FIRST);
    foreach ($fileIt as $fileInfo) {
      if ($fileInfo->isDir()) self::makeDirectory($dstDirPath . '/' . $fileIt->getSubPathName());
      else self::copyFile($fileInfo, $dstDirPath . '/' . $fileIt->getSubPathName());
    }
  }

  /**
   * 
   * Delete directory or file
   *
   * @param string[] $paths
   * @param bool $removeRootDir
   */
  public static function delete(...$paths) {
    $removeRootDir = true;
    if (is_bool(end($paths))) {
      $removeRootDir = end($paths);
      unset($paths[count($paths) - 1]);
    }
    foreach ($paths as $path) {
      if (is_file($path)) {
        unlink($path);
        continue;
      }
      $dirIt = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
      $fileIt = new \RecursiveIteratorIterator($dirIt, \RecursiveIteratorIterator::CHILD_FIRST);
      foreach ($fileIt as $fileInfo) {
        if ($fileInfo->isDir()) {
          rmdir($fileInfo);
        } else {
          unlink($fileInfo);
        }
      }
      if ($removeRootDir) {
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
  public static function replace(string $path, array $replace) {
    $content = file_get_contents($path);
    $content = str_replace(array_keys($replace), array_values($replace), $content);
    file_put_contents($path, $content, LOCK_EX);
  }

  /**
   * 
   * Find file
   * 
   * @param  string $pattern
   * @return array
   */
  public static function find(string $pattern): array {
    $files = [];
    foreach (glob($pattern) as $file) $files[] = basename($file);
    return $files;
  }

  /**
   * 
   * Find only one file
   * 
   * @param  string $pattern
   * @return string
   */
  public static function findOne(string $pattern): ?string {
    $files = self::find($pattern);
    if (empty($files)) return null;
    return $files[0];
  }

  /**
   * 
   * Find random file name
   * 
   * @param  string $pattern
   * @return string
   */
  public static function findRandomFileName(string $pattern): string {
    $files = self::find($pattern);
    $key = array_rand($files, 1);
    return $files[$key];
  }

  /**
   * 
   * Find random file conent
   * 
   * @param  string $pattern
   * @return string
   */
  public static function getRandomFileContent(string $pattern): string {
    return file_get_contents(dirname($pattern) . '/' . self::findRandomFileName($pattern));
  }

  /**
   * Get MimeType from file contents
   * 
   * @param  string $filePath
   * @return string
   */
  public static function getMimeByConent(string $filePath): string {
    if (!file_exists($filePath)) throw new \RuntimeException('Not found file ' . $filePath);
    else if (!is_file($filePath)) throw new \RuntimeException($filePath . ' is not file');
    $finfo = new \finfo(FILEINFO_MIME_TYPE);
    return $finfo->file($filePath);
  }


  /**
   * Verify that the file is of the specified Mime type
   * 
   * @param  string $filePath
   * @param  string $mime
   * @return bool
   */
  public static function validationMime(string $filePath, string $mime): bool{
    return self::getMimeByConent($filePath) ===  $mime;
  }

  /**
   * Returns the total size of all files in the directory in bytes.
   * 
   * @example
   * use \X\Util\FileHelper;
   *
   * // Returns the total size of all files in a directory
   * FileHelper::getDirectorySize('/var/log');
   *
   * // Returns the total size of all files in multiple directories
   * FileHelper::getDirectorySize([ '/var/log/php-fpm' '/var/log/nginx' ]);
   * 
   * @param  string|array $dirPaths
   * @param  SplFileInfo[] $result
   * @return int
   */
  public static function getDirectorySize($dirPaths, array &$result = []): int {
    if (is_string($dirPaths)) $dirPaths = [ $dirPaths ];
    else if (!is_array($dirPaths)) throw new RuntimeException('The file path type only allows strings or arrays of strings.');
    $size = 0;
    foreach ($dirPaths as $dirPath) {
      $dirIt = new \RecursiveDirectoryIterator($dirPath, \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS);
      $fileIt = new \RecursiveIteratorIterator($dirIt);
      foreach($fileIt as $fileInfo) {
        $result[] = $fileInfo;
        $size += $fileInfo->getSize();
      }
    }
    return $size;
  }
}