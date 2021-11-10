<?php
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
   * @param string $dir
   * @param int $mode
   * @return void
   */
  public static function makeDirectory(string $dir, int $mode = 0755) {
    try {
      if (file_exists($dir))
        return;
      if (@mkdir($dir, $mode, true) === false)
        throw new \RuntimeException('Cant create directory ' . $dir);
    } catch (\Throwable $e) {
      Logger::info($e->getMessage());
    }
  }

  /**
   * 
   * Move
   * 
   * ```php
   * // /tmp/old.txt -> /home/new.txt
   * \X\Util\FileHelper::move('/tmp/old.txt', '/home/new.txt');
   * // /tmp/old.txt -> ./tmp/new.txt
   * \X\Util\FileHelper::move('/tmp/old.txt', 'new.txt');
   * // /tmp/old.txt -> ./tmp/new.txt
   * \X\Util\FileHelper::move('/tmp/old.txt', 'new');
   * ```
   * 
   * @throws RuntimeException
   * @param string     $srcFile
   * @param string     $dstFile
   * @param string|int $group
   * @param string|int $user
   * @return void
   */
  public static function move(string $srcFile, string $dstFile, $group = null, $user = null) {
    if (!file_exists($srcFile))
      throw new \RuntimeException('Not found file ' . $srcFile);
    if (strpos($dstFile, '/') === false) {
      if (strpos($dstFile, '.') === false)
        $dstFile = $dstFile . '.' . pathinfo($srcFile, PATHINFO_EXTENSION);
      $dstFile = pathinfo($srcFile, PATHINFO_DIRNAME) . '/' . $dstFile;
    } else
      self::makeDirectory(dirname($dstFile));
    if (rename($srcFile, $dstFile) === false)
      throw new \RuntimeException('Can not rename from ' . $srcFile . ' to ' . $dstFile);
    if (isset($group))
      chgrp($dstFile, $group);
    if (isset($user))
      $res = chown($dstFile, $user);
  }

  /**
   * 
   * Copy file
   * 
   * @throws RuntimeException
   * @param string     $srcFile
   * @param string     $dstFile
   * @param string|int $group
   * @param string|int $user
   * @return void
   */
  public static function copyFile(string $srcFile, string $dstFile, $group = null, $user = null) {
    if (!file_exists($srcFile))
      throw new \RuntimeException('Not found file ' . $srcFile);
    else if (!is_file($srcFile))
      throw new \RuntimeException($srcFile . ' is not file');
    self::makeDirectory(dirname($dstFile));
    if (copy($srcFile, $dstFile) === false)
      throw new \RuntimeException('Can not copy from ' . $srcFile . ' to ' . $dstFile);
    if (isset($group))
      chgrp($dstFile, $group);
    if (isset($user))
      chown($dstFile, $user);
  }

  /**
   * 
   * Copy directory
   *
   * @throws RuntimeException
   * @param string $srcDir
   * @param string $dstDir
   * @return void
   */
  public static function copyDirectory(string $srcDir, string $dstDir) {
    if (!file_exists($srcDir))
      throw new \RuntimeException('Not found directory ' . $srcDir);
    else if (!is_dir($srcDir))
      throw new \RuntimeException($srcDir . ' is not directory');
    self::makeDirectory($dstDir);
    $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($srcDir, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
    foreach ($it as $info) {
      if ($info->isDir())
        self::makeDirectory($dstDir . '/' . $it->getSubPathName());
      else
        self::copyFile($info, $dstDir . '/' . $it->getSubPathName());
    }
  }

  /**
   * Delete directory or file
   * 
   * @example
   * use \X\Util\FileHelper;
   * 
   * // Delete all files and folders in "/ path"..
   * FileHelper::delete('/test');
   * 
   * // Delete all files and folders in the "/ path" folder and also in the "/ path" folder.
   * $deleteRoute = true;
   * FileHelper::delete('/test', $deleteRoute);
   * 
   * // Lock before deleting, Locks are disabled by default.
   * $deleteRoute = true;
   * $enableLock = true;
   * FileHelper::delete('/test', $deleteRoute, $enableLock);
   * 
   * @param string[] $paths
   */
  public static function delete(...$paths) {
    // If the parameter path is an array rather than a variadic argument, target the first element.
    if (is_array(reset($paths)))
      $paths = reset($paths);

    // Get options.
    $deleteRoute = true;
    $enableLock = false;
    if (count($paths) > 2 && is_bool(end($paths)) && is_bool($paths[count($paths) - 2])) {
      $enableLock = end($paths);
      unset($paths[count($paths) - 1]);
      $deleteRoute = end($paths);
      unset($paths[count($paths) - 1]);
    } else if (count($paths) > 1 && is_bool(end($paths))) {
      $deleteRoute = end($paths);
      unset($paths[count($paths) - 1]);
    }
    // Logger::debug('$deleteRoute=', $deleteRoute ? 1 : 0);
    // Logger::debug('$enableLock=', $enableLock ? 1 : 0);
    // Logger::debug('$paths=', $paths);

    // Recursively delete the specified path.
    foreach ($paths as $path) {
      if (!file_exists($path)) {
        Logger::error("{$path} not found");
      } else if (is_file($path)) {
        // If it's a file, simply delete it.
        unlink($path);
      } else {
        // For directories, recursively delete files and directories under them.
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($it as $info) {
          Logger::debug('Remove ' . $info->getPathname());
          if ($info->isDir())
            rmdir($info);
          else {
            if ($enableLock) {
              // 'w' mode truncates file, you don't want to do that yet!
              $fp = fopen($info->getPathname(), 'c');

              // blocking, but you can use LOCK_EX | LOCK_NB for nonblocking and a loop + sleep(1) for a timeout
              flock($fp, LOCK_EX);

              // truncate file to 0 length
              ftruncate($fp, 0);
              fclose($fp);
              unlink($info->getPathname());
            } else {
              unlink($info);
            }
          }
        }
        if ($deleteRoute)
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
   * Find file
   * 
   * ```php
   * use \X\Util\FileHelper;
   *
   * // Search only image files.
   * FileHelper::find('/img/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
   * ```
   *
   * @param  string      $pattern 
   * @param  int|integer $flags
   *                     Valid flags:
   *                     GLOB_MARK - Adds a slash (a backslash on Windows) to each directory returned
   *                     GLOB_NOSORT - Return files as they appear in the directory (no sorting). When this flag is not used, the pathnames are sorted alphabetically
   *                     GLOB_NOCHECK - Return the search pattern if no files matching it were found
   *                     GLOB_NOESCAPE - Backslashes do not quote metacharacters
   *                     GLOB_BRACE - Expands {a,b,c} to match 'a', 'b', or 'c'
   *                     GLOB_ONLYDIR - Return only directory entries which match the pattern
   *                     GLOB_ERR - Stop on read errors (like unreadable directories), by default errors are ignored.
   * @return array
   */
  public static function find(string $pattern, int $flags = 0): array {
    $files = [];
    foreach (glob($pattern, $flags) as $file)
      $files[] = basename($file);
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
    if (empty($files))
      return null;
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
   * @param  string $file
   * @return string
   */
  public static function getMimeByConent(string $file): string {
    if (!file_exists($file))
      throw new \RuntimeException('Not found file ' . $file);
    else if (!is_file($file))
      throw new \RuntimeException($file . ' is not file');
    $finfo = new \finfo(FILEINFO_MIME_TYPE);
    return $finfo->file($file);
  }


  /**
   * Verify that the file is of the specified Mime type
   * 
   * @param  string $file
   * @param  string $mime
   * @return bool
   */
  public static function validationMime(string $file, string $mime): bool{
    return self::getMimeByConent($file) ===  $mime;
  }

  /**
   * Returns the total size of all files in the directory in bytes.
   * 
   * ```php
   * use \X\Util\FileHelper;
   *
   * // Returns the total size of all files in a directory
   * FileHelper::getDirectorySize('/var/log');
   *
   * // Returns the total size of all files in multiple directories
   * FileHelper::getDirectorySize([ '/var/log/php-fpm' '/var/log/nginx' ]);
   * ```
   *
   * @param  string|array $dirs
   * @param  SplFileInfo[] $infos
   * @return int
   */
  public static function getDirectorySize($dirs, array &$infos = []): int {
    if (is_string($dirs)) $dirs = [ $dirs ];
    else if (!is_array($dirs)) throw new RuntimeException('The file path type only allows strings or arrays of strings');
    $size = 0;
    foreach ($dirs as $dir) {
      $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS));
      foreach($it as $info) {
        $infos[] = $info;
        $size += $info->getSize();
      }
    }
    return $size;
  }

  /**
   * Returns the file size with units.
   *
   * ```php
   * use \X\Util\FileHelper;
   *
   * FileHelper::humanFilesize('/var/somefile.txt', 0);// 12B
   * FileHelper::humanFilesize('/var/somefile.txt', 4);// 1.1498GB
   * FileHelper::humanFilesize('/var/somefile.txt', 1);// 117.7MB
   * FileHelper::humanFilesize('/var/somefile.txt', 5);// 11.22833TB
   * FileHelper::humanFilesize('/var/somefile.txt', 3);// 1.177MB
   * FileHelper::humanFilesize('/var/somefile.txt');// 120.56KB
   * ```
   * 
   * @param  string      $filepath File Path
   * @param  int|integer $decimals Decimal digits
   * @return string                File size with units 
   */
  public static function humanFilesize(string $filepath, int $decimals = 2): string {
    $bytes = 0;
    if (file_exists($filepath)) {
      clearstatcache(true, $filepath);
      $bytes = filesize($filepath);
    }
    $factor = floor((strlen($bytes) - 1) / 3);
    if ($factor > 0)
      $sz = 'KMGT';
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor - 1] . 'B';
  }
}

