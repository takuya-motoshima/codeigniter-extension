<?php

/**
 * Video helper class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Util;
use \X\Util\FileHelper;
use \X\Util\Logger;

final class VideoHelper {

  /**
   * 
   * Put base64 video.
   *
   * ```php
   * use \X\Util\VideoHelper;
   * 
   * VideoHelper::putBase64('data:video/mp4;base64,iVBOR...', '/tmp', 'sample');
   * VideoHelper::putBase64('data:video/mp4;base64,iVBOR...', '/tmp/sample.mp4');
   * ```
   *
   * @param string $base64
   * @param string $dir
   * @return string Return file name
   */
  public static function putBase64(string $base64, string $dir, ?string $fileName = null): string {
    if (empty($fileName)) {
      $fileName = pathinfo($dir, PATHINFO_BASENAME);
      $dir =  pathinfo($dir, PATHINFO_DIRNAME);
    }
    $dir = rtrim($dir, '/')  . '/';
    $blob = self::convertBase64ToBlob($base64, $mime);
    if (empty(pathinfo($fileName, PATHINFO_EXTENSION))) {
      $fileName .= '.' . $mime;
    }
    FileHelper::makeDirectory($dir);
    file_put_contents($dir . $fileName, $blob, LOCK_EX);
    return $fileName;
  }

  /**
   * 
   * Convert Base64 to blob
   *
   * @param string $base64
   * @param string &$mime
   * @return \stdClass
   */
  public static function convertBase64ToBlob(string $base64, &$mime = null): string {
    if (!self::isBase64($base64, $mime)) {
      throw new \RuntimeException('Did not match data URI with video data');
    }
    $blob = base64_decode(substr($base64, strpos($base64, ',') + 1));
    if ($blob === false) {
      throw new \RuntimeException('Base64 decode failed');
    }
    return $blob;
  }

  /**
   * 
   * Is Base64
   *
   * @param string $base64
   * @param string &$mime
   * @return \stdClass
   */
  public static function isBase64(string $base64, &$mime = null): bool {
    if (!preg_match('/^data:video\/(\w+);base64,/', $base64, $matches)) {
      return false;
    }
    $mime = strtolower($matches[1]);
    return true;
  }
}