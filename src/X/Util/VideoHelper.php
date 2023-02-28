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
   * VideoHelper::putBase64('data:video/mp4;base64,iVBOR...', '/tmp', 'sample');
   * VideoHelper::putBase64('data:video/mp4;base64,iVBOR...', '/tmp/sample.mp4');
   * ```
   */
  public static function putBase64(string $base64, string $outputDir, ?string $outputName = null): string {
    if (empty($outputName)) {
      $outputName = pathinfo($outputDir, PATHINFO_BASENAME);
      $outputDir =  pathinfo($outputDir, PATHINFO_DIRNAME);
    }
    $outputDir = rtrim($outputDir, '/')  . '/';
    $blob = self::convertBase64ToBlob($base64, $mime);
    if (empty(pathinfo($outputName, PATHINFO_EXTENSION)))
      $outputName .= '.' . $mime;
    FileHelper::makeDirectory($outputDir);
    file_put_contents($outputDir . $outputName, $blob, LOCK_EX);
    return $outputName;
  }

  /**
   * Convert Base64 to blob.
   */
  public static function convertBase64ToBlob(string $base64, &$mime = null): string {
    if (!self::isBase64($base64, $mime))
      throw new \RuntimeException('Did not match data URI with video data');
    $blob = base64_decode(substr($base64, strpos($base64, ',') + 1));
    if ($blob === false)
      throw new \RuntimeException('Base64 decode failed');
    return $blob;
  }

  /**
   * Is Base64.
   */
  public static function isBase64(string $base64, &$mime = null): bool {
    if (!preg_match('/^data:video\/(\w+);base64,/', $base64, $matches))
      return false;
    $mime = strtolower($matches[1]);
    return true;
  }
}