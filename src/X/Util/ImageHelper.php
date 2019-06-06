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
   * @return \stdClass
   */
  public static function putBase64(string $base64, string $dirPath, ?string $fileName = null): \stdClass
  {
    if (empty($fileName)) {
      $fileName = pathinfo($dirPath, PATHINFO_BASENAME);
      $dirPath =  pathinfo($dirPath, PATHINFO_DIRNAME);
    }
    $dirPath = rtrim($dirPath, '/')  . '/';
    $blob = self::convertBase64ToBlob($base64, $mime);
    $extension = pathinfo($fileName, PATHINFO_EXTENSION);
    if (empty($extension)) {
      $baseName = $fileName . '.' . $mime;
    } else {
      $baseName = $fileName;
      $mime = $extension;
    }
    FileHelper::makeDirecoty($dirPath);
    file_put_contents($dirPath . $baseName, $blob, LOCK_EX);
    return json_decode(json_encode([
      'mime' => $mime,
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
    FileHelper::makeDirecoty(dirname($filePath));
    file_put_contents($filePath, $blob, LOCK_EX);
  }

  /**
   * 
   * Copy image.
   *
   * e.g:
   *  // /tmp/example.png -> /home/example.png
   *  \X\Util\ImageHelper::copy('/tmp/example.png', '/home');
   *
   *  // /tmp/old.png -> /home/new.png
   *  \X\Util\ImageHelper::copy('/tmp/old.png', '/home', 'new');
   *  
   * @param string $srcImgPath
   * @param string $dstDirPath
   * @param string $replacementImgName
   * @return string
   */
  public static function copy(string $srcImgPath, string $dstDirPath, string $replacementImgName = null): string
  {
    FileHelper::makeDirecoty($dstDirPath);
    $dstImgName = empty($replacementImgName) 
      ? basename($srcImgPath) : 
      $replacementImgName . '.' . pathinfo($srcImgPath, PATHINFO_EXTENSION);
    file_put_contents(rtrim($dstDirPath, '/')  . '/' . $dstImgName, file_get_contents($srcImgPath), LOCK_EX);
    return $dstImgName;
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
   * @param  string $srcImgPath
   * @param  int $dstWidth
   * @param  string $dstFilePrefix
   * @return string
   */
  public static function resize(string $srcImgPath, int $dstWidth, string $dstFilePrefix = "-thumb"): string
  {
    list($srcWidth, $srcHeight, $type) = \getimagesize($srcImgPath);
    $dstHeight = round($srcHeight * $dstWidth / $srcWidth);
    $tmpImg = \imagecreatetruecolor($dstWidth, $dstHeight);
    if ($tmpImg === FALSE) {
      throw new \RuntimeException('TrueColor image creation failed');
    }
    if ($type == IMAGETYPE_JPEG) {
      $dstImg = \imagecreatefromjpeg($srcImgPath);
    } else if ($type == IMAGETYPE_GIF) {
      $dstImg = \imagecreatefromgif($srcImgPath);
    } else if ($type == IMAGETYPE_PNG) {
      imagealphablending($tmpImg, false);
      imagesavealpha($tmpImg, true);
      $dstImg = \imagecreatefrompng($srcImgPath);
    }
    // else if ($type == IMAGETYPE_BMP) {
    //   $dstImg = \imagecreatefromwbmp($srcImgPath);
    // }
    \imagecopyresampled(
      $tmpImg,
      $dstImg,
      0,
      0,
      0,
      0,
      $dstWidth,
      $dstHeight,
      $srcWidth,
      $srcHeight
    );
    $dstImgName = pathinfo($srcImgPath, PATHINFO_FILENAME) . $dstFilePrefix . '.' . pathinfo($srcImgPath, PATHINFO_EXTENSION);
    $dstImgPath = rtrim(pathinfo($srcImgPath, PATHINFO_DIRNAME), '/') . '/' . $dstImgName;
    $quality = 100;
    if ($type == IMAGETYPE_JPEG) {
      \imagejpeg($tmpImg, $dstImgPath, $quality);
    } else if ($type == IMAGETYPE_GIF) {
      $bgColor = \imagecolorallocatealpha($dstImg,0,0,0,127);
      \imagefill($tmpImg, 0, 0, $bgColor);
      \imagecolortransparent($tmpImg, $bgColor);
      \imagegif($tmpImg, $dstImgPath);
    } else if ($type == IMAGETYPE_PNG) {
      \imagepng($tmpImg, $dstImgPath, $quality * (9 / 100));
    }
    // else if ($type == IMAGETYPE_BMP:
    //   \imagewbmp($tmpImg, $dstImgPath);
    // }
    imagedestroy($tmpImg);
    imagedestroy($dstImg);
    return $dstImgName;
  }

  /**
   * 
   * Is Base64
   *
   * @param string $base64
   * @param string &$mime
   * @return \stdClass
   */
  public static function isBase64(string $base64, &$mime = null): bool
  {
    if (!preg_match('/^data:image\/(\w+);base64,/', $base64, $matches)) {
      return false;
    }
    $mime = strtolower($matches[1]);
    return true;
  }


  /**
   * 
   * Convert Base64 to blob
   *
   * @param string $base64
   * @param string &$mime
   * @return \stdClass
   */
  public static function convertBase64ToBlob(string $base64, &$mime = null): string
  {
    if (!self::isBase64($base64, $mime)) {
      throw new \RuntimeException('Did not match data URI with image data');
    }
    $blob = base64_decode(substr($base64, strpos($base64, ',') + 1));
    if ($blob === false) {
      throw new \RuntimeException('Base64 decode failed');
    }
    return $blob;
  }
}