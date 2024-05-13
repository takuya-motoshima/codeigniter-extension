<?php
namespace X\Util;
use \X\Util\FileHelper;
use \X\Util\Logger;

final class VideoHelper {
  /**
   * Put base64 video.
   * ```php
   * use \X\Util\VideoHelper;
   * 
   * VideoHelper::writeDataURLToFile('data:video/mp4;base64,iVBOR...', '/tmp', 'sample');
   * VideoHelper::writeDataURLToFile('data:video/mp4;base64,iVBOR...', '/tmp/sample.mp4');
   * ```
   */
  public static function writeDataURLToFile(string $dataURL, string $dir, ?string $filename=null): string {
    if (empty($filename)) {
      $filename = pathinfo($dir, PATHINFO_BASENAME);
      $dir =  pathinfo($dir, PATHINFO_DIRNAME);
    }
    $dir = rtrim($dir, '/')  . '/';
    $blob = self::dataURL2Blob($dataURL, $mime);
    if (empty(pathinfo($filename, PATHINFO_EXTENSION)))
      $filename .= '.' . $mime;
    FileHelper::makeDirectory($dir);
    file_put_contents($dir . $filename, $blob, LOCK_EX);
    return $filename;
  }

  /**
   * Convert Base64 to blob.
   */
  public static function dataURL2Blob(string $dataURL, &$mime=null): string {
    if (!self::isDataURL($dataURL, $mime))
      throw new \RuntimeException('Did not match data URI with video data');
    $blob = base64_decode(substr($dataURL, strpos($dataURL, ',') + 1));
    if ($blob === false)
      throw new \RuntimeException('Base64 decode failed');
    return $blob;
  }

  /**
   * Is Base64.
   */
  public static function isDataURL(string $dataURL, &$mime=null): bool {
    if (!preg_match('/^data:video\/(\w+);base64,/', $dataURL, $matches))
      return false;
    $mime = strtolower($matches[1]);
    return true;
  }
}