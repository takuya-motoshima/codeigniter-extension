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
use \Intervention\Image\ImageManager;

final class ImageHelper {

  /**
   * 
   * Put base64 image.
   *
   * ```php
   * use \X\Util\ImageHelper;
   *
   * ImageHelper::putBase64('data:image/png;base64,iVBOR...', '/tmp', 'sample');
   * ImageHelper::putBase64('data:image/png;base64,iVBOR...', '/tmp/sample.png');
   * ```
   *
   * @param string $base64
   * @param string $dir
   * @return string Return file name
   */
  public static function putBase64(string $base64, string $dir, ?string $filename = null): string {
    if (empty($filename)) {
      $filename = pathinfo($dir, PATHINFO_BASENAME);
      $dir =  pathinfo($dir, PATHINFO_DIRNAME);
    }
    $dir = rtrim($dir, '/')  . '/';
    $blob = self::convertBase64ToBlob($base64, $mime);
    if (empty(pathinfo($filename, PATHINFO_EXTENSION))) {
      $filename .= '.' . $mime;
    }
    FileHelper::makeDirectory($dir);
    file_put_contents($dir . $filename, $blob, LOCK_EX);
    return $filename;
  }

  /**
   * 
   * Put blob image
   *
   * @param string $blob
   * @param string $filepath
   * @return Void
   */
  public static function putBlob(string $blob, string $filepath) {
    FileHelper::makeDirectory(dirname($filepath));
    file_put_contents($filepath, $blob, LOCK_EX);
  }

  /**
   * 
   * Copy image.
   *
   * ```php
   * // /tmp/example.png -> /home/example.png
   * \X\Util\ImageHelper::copy('/tmp/example.png', '/home');
   *
   * // /tmp/old.png -> /home/new.png
   * \X\Util\ImageHelper::copy('/tmp/old.png', '/home', 'new');
   * ```
   * 
   * @param string $srcImgPath
   * @param string $dstDirpath
   * @param string $dstImgName
   * @return string
   */
  public static function copy(string $srcImgPath, string $dstDirpath, string $dstImgName = null): string {
    FileHelper::makeDirectory($dstDirpath);
    $dstImgName = empty($dstImgName) 
      ? basename($srcImgPath) : 
      $dstImgName . '.' . pathinfo($srcImgPath, PATHINFO_EXTENSION);
    file_put_contents(rtrim($dstDirpath, '/')  . '/' . $dstImgName, file_get_contents($srcImgPath), LOCK_EX);
    return $dstImgName;
  }

  /**
   * 
   * Read image
   *
   * @param string $filepath
   * @return string
   */
  public static function read(string $filepath): string {
    if (!file_exists($filepath)) {
      throw new \RuntimeException('Image file does not exist. Path=' . $filepath);
    }
    $fp = fopen($filepath, 'r');
    $blob = fread($fp, filesize($filepath));
    fclose($fp);
    return $blob;
  }

  /**
   * 
   * Read image
   *
   * @param string $filepath
   * @return string
   */
  public static function readAsBase64(string $filepath): string {
    $blob = self::read($filepath);
    $mime = mime_content_type($filepath);
    if ($mime === 'image/svg' || $mime === 'image/svgz') {
      $mime = 'image/svg+xml';
    }
    return 'data:' . $mime . ';base64,' . base64_encode($blob);
  }

  /**
   * Resize
   * 
   * @param  string       $filepath
   * @param  string       $resizepath
   * @param  int|null     $width
   * @param  int|null     $height
   * @param  bool|boolean $aspectRatio True if you want to keep the aspect ratio
   */
  public static function resize(
    string $filepath,
    string $resizepath,
    ?int $width = null,
    ?int $height = null,
    bool $aspectRatio = true
  ) {
    // resize the image to a width of 300 and constrain aspect ratio (auto height)
    $manager = new ImageManager(['driver' => 'gd']);
    $manager
      ->make($filepath)
      ->resize($width, $height, function ($constraint) use($aspectRatio) {
        if ($aspectRatio) $constraint->aspectRatio();
      })
      ->save($resizepath);
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
  public static function convertBase64ToBlob(string $base64, &$mime = null): string {
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