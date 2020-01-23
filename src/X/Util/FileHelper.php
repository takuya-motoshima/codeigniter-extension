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
final class FileHelper {

  /**
   * 
   * Make directory
   *
   * @throws RuntimeException
   * @param string $dir
   * @param int $mode
   * @return void
   */
  public static function makeDirectory(string $dir, int $mode = 0755) {
    if (file_exists($dir)) {
      return;
    }
    if (mkdir($dir, $mode, true) === false) {
      throw new \RuntimeException('Cant create directory ' . $dir);
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
    if (!file_exists($srcFilepath)) {
      throw new \RuntimeException('Not found file ' . $srcFilepath);
    }
    if (strpos($dstFilepath, '/') === false) {
      if (strpos($dstFilepath, '.') === false) {
        $dstFilepath = $dstFilepath . '.' . pathinfo($srcFilepath, PATHINFO_EXTENSION);
      }
      $dstFilepath = pathinfo($srcFilepath, PATHINFO_DIRNAME) . '/' . $dstFilepath;
    } else {
      self::makeDirecoty(dirname($dstFilepath));;
    }
    if (rename($srcFilepath, $dstFilepath) === false) {
      throw new \RuntimeException('Can not rename from ' . $srcFilepath . ' to ' . $dstFilepath);
    }
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
    if (!file_exists($srcFilepath)) {
      throw new \RuntimeException('Not found file ' . $srcFilepath);
    } else if (!is_file($srcFilepath)) {
      throw new \RuntimeException($srcFilepath . ' is not file');
    }
    self::makeDirecoty(dirname($dstFilepath));
    if (copy($srcFilepath, $dstFilepath) === false) {
      throw new \RuntimeException('Can not copy from ' . $srcFilepath . ' to ' . $dstFilepath);
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
  public static function copyDirectory(string $srcDirPath, string $dstDirPath) {
    if (!file_exists($srcDirPath)) {
      throw new \RuntimeException('Not found directory ' . $srcDirPath);
    } else if (!is_dir($srcDirPath)) {
      throw new \RuntimeException($srcDirPath . ' is not directory');
    }
    self::makeDirecoty($dstDirPath);
    $iterator = new \RecursiveIteratorIterator(
      new \RecursiveDirectoryIterator($srcDirPath, \RecursiveDirectoryIterator::SKIP_DOTS),
      \RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($iterator as $file) {
      if ($file->isDir()) {
        self::makeDirecoty($dstDirPath . '/' . $iterator->getSubPathName());
      } else {
        self::copyFile($file, $dstDirPath . '/' . $iterator->getSubPathName());
      }
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
    foreach (glob($pattern) as $file) {
      $files[] = basename($file);
    }
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
    if (empty($files)) {
      return null;
    }
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
    if (!file_exists($filePath)) {
      throw new \RuntimeException('Not found file ' . $filePath);
    } else if (!is_file($filePath)) {
      throw new \RuntimeException($filePath . ' is not file');
    }
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
}