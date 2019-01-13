<?php

/**
 * Image helper class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Util;
use \X\Util\FileHelper;
use \X\Util\Logger;
final class ImageHelper
{

  /**
   * 
   * Put base64 image.
   *
   * @param string $base64
   * @param string $dirPath
   * @return stdClass
   */
  public static function putBase64(string $base64, string $dirPath, string $fileName): stdClass
  {
    $blobInfo = self::base64ToBlob($base64);
    $baseName = $fileName . '.' . $blobInfo->extension;
    FileHelper::makeDirecoty($dirPath);
    file_put_contents(rtrim($dirPath, '/')  . '/' . $baseName, $blobInfo->blob);
    return json_decode(json_encode([
      'extension' => $blobInfo->extension,
      'fileName' => $fileName,
      'baseName' => $baseName,
    ]));
  }

  /**
   * 
   * Put blob image
   *
   * @param string $blob
   * @param string $filePath
   * @return Void
   */
  public static function putBlob(string $blob, string $filePath)
  {
    if (\ENVIRONMENT !== 'production') {
      Logger::d('$blob=', $blob);
      Logger::d('$filePath=', $filePath);
    }
    FileHelper::makeDirecoty(dirname($filePath));
    file_put_contents($filePath, $blob);
  }

  /**
   * 
   * Copy image.
   *
   * @param string $srcFilePath
   * @param string $dstDirPath
   * @param string $replacementFileName
   * @return string
   */
  public static function copy(string $srcFilePath, string $dstDirPath, string $replacementFileName = null): string
  {
    FileHelper::makeDirecoty($dstDirPath);
    $dstFileName = basename($srcFilePath);
    if (!empty($replacementFileName)) {
      $dstFileName = preg_replace('/..*(\...*)$/', $replacementFileName . '$1', $dstFileName);
    }
    file_put_contents(
      rtrim($dstDirPath, '/')  . '/' . $dstFileName, 
      file_get_contents($srcFilePath)
    );
    return $dstFileName;
  }

  /**
   * 
   * Read image
   *
   * @param string $filePath
   * @return string
   */
  public static function read(string $filePath): string
  {
    if (!file_exists($filePath)) {
      throw new \RuntimeException('Image file does not exist. Path=' . $filePath);
    }
    $fp = fopen($filePath, 'r');
    $blob = fread($fp, filesize($filePath));
    fclose($fp);
    return $blob;
  }


  /**
   * 
   * Resize
   *
   * @param  string $srcFilePath
   * @param  int $dstWidth
   * @param  string $dstFilePrefix
   * @return string
   */
  public static function resize(string $srcFilePath, int $dstWidth, string $dstFilePrefix = "-thumb"): string
  {
    list($srcWidth, $srcHeight, $fileType) = \getimagesize($srcFilePath);
    $dstHeight = round($srcHeight * $dstWidth / $srcWidth);
    $tmpImage = \imagecreatetruecolor($dstWidth, $dstHeight);
    if ($tmpImage === FALSE) {
      throw new \RuntimeException('TrueColor image creation failed');
    }
    if ($fileType == IMAGETYPE_JPEG) {
      $dstImage = \imagecreatefromjpeg($srcFilePath);
    } else if ($fileType == IMAGETYPE_GIF) {
      $dstImage = \imagecreatefromgif($srcFilePath);
    } else if ($fileType == IMAGETYPE_PNG) {
      imagealphablending($tmpImage, false);
      imagesavealpha($tmpImage, true);
      $dstImage = \imagecreatefrompng($srcFilePath);
    }
    // else if ($fileType == IMAGETYPE_BMP) {
    //   $dstImage = \imagecreatefromwbmp($srcFilePath);
    // }
    \imagecopyresampled(
      $tmpImage,
      $dstImage,
      0,
      0,
      0,
      0,
      $dstWidth,
      $dstHeight,
      $srcWidth,
      $srcHeight
    );
    $dstFileName = pathinfo($srcFilePath, PATHINFO_FILENAME) . $dstFilePrefix . '.' . pathinfo($srcFilePath, PATHINFO_EXTENSION);
    $dstFilePath = rtrim(pathinfo($srcFilePath, PATHINFO_DIRNAME), '/') . '/' . $dstFileName;
    $quality = 100;
    if ($fileType == IMAGETYPE_JPEG) {
      \imagejpeg($tmpImage, $dstFilePath, $quality);
    } else if ($fileType == IMAGETYPE_GIF) {
      $bg_color = \imagecolorallocatealpha($dstImage,0,0,0,127);
      \imagefill($tmpImage, 0, 0, $bg_color);
      \imagecolortransparent($tmpImage, $bg_color);
      \imagegif($tmpImage, $dstFilePath);
    } else if ($fileType == IMAGETYPE_PNG) {
      \imagepng($tmpImage, $dstFilePath, $quality * (9 / 100));
    }
    // else if ($fileType == IMAGETYPE_BMP:
    //   \imagewbmp($tmpImage, $dstFilePath);
    // }
    imagedestroy($tmpImage);
    imagedestroy($dstImage);
    return $dstFileName;
  }

  /**
   * 
   * Base64 to blob
   *
   * @param string $base64
   * @return stdClass
   */
  public static function base64ToBlob(string $base64): stdClass
  {
    if (!preg_match('/^data:image\/(\w+);base64,/', $base64, $extension)) {
      throw new \RuntimeException('Did not match data URI with image data');
    }
    $extension = strtolower($extension[1]);
    $blob = base64_decode(substr($base64, strpos($base64, ',') + 1));
    if ($blob === false) {
      throw new \RuntimeException('Base64 decode failed');
    }
    return json_decode(json_encode([
      'blob' => $blob,
      'extension' => $extension, 
    ]));
  }
}