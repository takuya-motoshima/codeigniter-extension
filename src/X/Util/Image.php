<?php

/**
 * Image util class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Util;
use \X\Util\FileUtil;
use \X\Util\Loader;
use \X\Util\Logger;
final class Image
{

  /**
   * 
   * Put base64 image.
   *
   * @param string $base64
   * @param string $dirPath
   * @return array
   */
  public static function putBase64(string $base64, string $dirPath, string $fileName): array
  {
    $info = self::base64_to_byte($base64);
    $baseName = $fileName . '.' . $info['extension'];
    FileUtil::make_direcoty($dirPath);
    file_put_contents(rtrim($dirPath, '/')  . '/' . $baseName, $info['source']);
    return [
      'extension' => $info['extension'],
      'file_name' => $fileName,
      'base_name' => $baseName,
    ];
  }

  /**
   * 
   * Put blob image
   *
   * @param string $blob
   * @param string $filePath
   * @return void
   */
  public static function putBlob(string $blob, string $filePath)
  {
    if (\ENVIRONMENT !== 'production') {
      Logger::d('$blob=', $blob);
      Logger::d('$filePath=', $filePath);
    }
    FileUtil::make_direcoty(dirname($filePath));
    file_put_contents($filePath, $blob);
  }

  /**
   * 
   * Copy image.
   *
   * @param string $orgFilePath
   * @param string $dirPath
   * @param string $replacementFileName
   * @return string
   */
  public static function copy(string $orgFilePath, string $dirPath, string $replacementFileName = null): string
  {
    FileUtil::make_direcoty($dirPath);
    $copyFileName = basename($orgFilePath);
    if (!empty($replacementFileName)) {
      $copyFileName = preg_replace('/..*(\...*)$/', $replacementFileName . '$1', $copyFileName);
    }
    file_put_contents(
      rtrim($dirPath, '/')  . '/' . $copyFileName, 
      file_get_contents($orgFilePath)
    );
    return $copyFileName;
  }


  /**
   * 
   * @deprecated Please use Image :: putBase64
   */
  public static function put_image_blob(string $blob, string $filePath)
  {
    self::putBlob($blob, $filePath);
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
      throw new \RuntimeException('Image file does not exist. image_path=' . $filePath);
    }
    $fp = fopen($filePath, 'r');
    $image = fread($fp, filesize($filePath));
    fclose($fp);
    return $image;
  }

  /**
   * 
   * Resize
   *
   * @param  string $filePath
   * @param  int $newWidth
   * @param  string $prefix
   * @return string
   */
  public static function resize(string $filePath, int $newWidth, string $prefix = "-thumb"): string
  {
    list($orgWidth, $orgHeight, $type) = \getimagesize($filePath);
    $newHeight = round($orgHeight * $newWidth / $orgWidth);
    $tmpImage = \imagecreatetruecolor($newWidth, $newHeight);
    if ($tmpImage === FALSE) {
      throw new \RuntimeException('TrueColor image creation failed');
    }
    switch($type){
    case IMAGETYPE_JPEG:
      $newImage = \imagecreatefromjpeg($filePath);
      break;
    case IMAGETYPE_GIF:
      $newImage = \imagecreatefromgif($filePath);
      break;
    case IMAGETYPE_PNG:
      imagealphablending($tmpImage, false);
      imagesavealpha($tmpImage, true);
      $newImage = \imagecreatefrompng($filePath);
      break;
    // case IMAGETYPE_BMP:
    //     // $newImage = \imagecreatefrombmp($filePath);
    //     $newImage = \imagecreatefromwbmp($filePath);
    //     break;
    }
    \imagecopyresampled(
      $tmpImage,
      $newImage,
      0,
      0,
      0,
      0,
      $newWidth,
      $newHeight,
      $orgWidth,
      $orgHeight
    );
    $newImageName = pathinfo($filePath, PATHINFO_FILENAME) . $prefix . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
    $newImagePath = rtrim(pathinfo($filePath, PATHINFO_DIRNAME), '/') . '/' . $newImageName;
    $quality = 100;
    switch($type){
    case IMAGETYPE_JPEG:
      \imagejpeg($tmpImage, $newImagePath, $quality);
      break;
    case IMAGETYPE_GIF:
      $bg_color = \imagecolorallocatealpha($newImage,0,0,0,127);
      \imagefill($tmpImage, 0, 0, $bg_color);
      \imagecolortransparent($tmpImage, $bg_color);
      \imagegif($tmpImage, $newImagePath);
      break;
    case IMAGETYPE_PNG:
      \imagepng($tmpImage, $newImagePath, $quality * (9 / 100));
      // \imagepng($tmpImage, $newImagePath, $quality);
      break;
    // case IMAGETYPE_BMP:
    //     // \imagebmp($tmpImage, $newImagePath);
    //     \imagewbmp($tmpImage, $newImagePath);
    //     break;
    }
    imagedestroy($tmpImage);
    imagedestroy($newImage);
    return $newImageName;
  }

  /**
   * 
   * Base64 to byte
   *
   * @param string $base64
   * @return array
   */
  public static function base64_to_byte(string $base64): array
  {
    if (!preg_match('/^data:image\/(\w+);base64,/', $base64, $extension)) {
      throw new \RuntimeException('Did not match data URI with image data');
    }
    $extension = strtolower($extension[1]);
    $blob = base64_decode(substr($base64, strpos($base64, ',') + 1));
    if ($blob === false) {
      throw new \RuntimeException('Base64 decode failed');
    }
    return [
      'extension' => $extension,
      'source' => $blob
    ];
  }
}
