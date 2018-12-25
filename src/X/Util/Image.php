<?php

/**
 * Image util class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Util;
use X\Util\FileUtil;
use X\Util\Loader;
final class Image
{

  /**
   * 
   * Put image blob
   *
   * @param string $imageBlob
   * @param string $imagePath
   * @return void
   */
  public static function put_image_blob(string $imageBlob, string $imagePath)
  {
    if (\ENVIRONMENT !== 'production') {
      Logger::d('$imageBlob=', $imageBlob);
      Logger::d('$imagePath=', $imagePath);
    }
    FileUtil::make_direcoty(dirname($imagePath));
    file_put_contents($imagePath, $imageBlob);
  }

  /**
   * 
   * Resize
   *
   * @param  string $str
   * @param  string $addReplacement
   * @return string Filename of thumbnail
   */
  public static function resize(string $imagePath, int $newWidth, string $prefix = "-thumb"): string
  {
    list($orgWidth, $orgHeight, $type) = \getimagesize($imagePath);
    $newHeight = round($orgHeight * $newWidth / $orgWidth);
    $tmpImage = \imagecreatetruecolor($newWidth, $newHeight);
    if ($tmpImage === FALSE) {
      throw new \RuntimeException('TrueColor image creation failed');
    }
    switch($type){
    case IMAGETYPE_JPEG:
      $newImage = \imagecreatefromjpeg($imagePath);
      break;
    case IMAGETYPE_GIF:
      $newImage = \imagecreatefromgif($imagePath);
      break;
    case IMAGETYPE_PNG:
      imagealphablending($tmpImage, false);
      imagesavealpha($tmpImage, true);
      $newImage = \imagecreatefrompng($imagePath);
      break;
    // case IMAGETYPE_BMP:
    //     // $newImage = \imagecreatefrombmp($imagePath);
    //     $newImage = \imagecreatefromwbmp($imagePath);
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
    $newImageName = pathinfo($imagePath, PATHINFO_FILENAME) . $prefix . '.' . pathinfo($imagePath, PATHINFO_EXTENSION);
    $newImagePath = rtrim(pathinfo($imagePath, PATHINFO_DIRNAME), '/') . '/' . $newImageName;
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
   * @param string $imageBase64
   * @return array
   */
  public static function base64_to_byte(string $imageBase64): array
  {
    if (!preg_match('/^data:image\/(\w+);base64,/', $imageBase64, $extension)) {
      throw new \RuntimeException('Did not match data URI with image data');
    }
    $extension = strtolower($extension[1]);
    $source = base64_decode(substr($imageBase64, strpos($imageBase64, ',') + 1));
    if ($source === false) {
      throw new \RuntimeException('Base64 decode failed');
    }
    return [
      'extension' => $extension,
      'source' => $source
    ];
  }
}
