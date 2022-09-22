<?php
namespace X\Util;
use \X\Util\FileHelper;
use \X\Util\Logger;
use \Intervention\Image\ImageManager;

final class ImageHelper {
  /**
   * Put base64 image.
   * <code>
   * <?php
   * use \X\Util\ImageHelper;
   *
   * ImageHelper::putBase64('data:image/png;base64,iVBOR...', '/tmp', 'sample');
   * ImageHelper::putBase64('data:image/png;base64,iVBOR...', '/tmp/sample.png');
   * </code>
   */
  public static function putBase64(string $base64, string $dir, ?string $fileName = null): string {
    if (empty($fileName)) {
      $fileName = pathinfo($dir, PATHINFO_BASENAME);
      $dir =  pathinfo($dir, PATHINFO_DIRNAME);
    }
    $dir = rtrim($dir, '/')  . '/';
    $blob = self::convertBase64ToBlob($base64, $mime);
    if (empty(pathinfo($fileName, PATHINFO_EXTENSION)))
      $fileName .= '.' . $mime;
    FileHelper::makeDirectory($dir);
    file_put_contents($dir . $fileName, $blob, LOCK_EX);
    return $fileName;
  }

  /**
   * Put blob image.
   */
  public static function putBlob(string $blob, string $filePath) {
    FileHelper::makeDirectory(dirname($filePath));
    file_put_contents($filePath, $blob, LOCK_EX);
  }

  /**
   * Copy image.
   * <code>
   * <?php
   * // /tmp/example.png -> /home/example.png
   * \X\Util\ImageHelper::copy('/tmp/example.png', '/home');
   *
   * // /tmp/old.png -> /home/new.png
   * \X\Util\ImageHelper::copy('/tmp/old.png', '/home', 'new');
   * </code>
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
   * Read image.
   */
  public static function read(string $filePath): string {
    if (!file_exists($filePath))
      throw new \RuntimeException('Image file does not exist. Path=' . $filePath);
    $fp = fopen($filePath, 'r');
    $blob = fread($fp, filesize($filePath));
    fclose($fp);
    return $blob;
  }

  /**
   * Read image.
   */
  public static function readAsBase64(string $filePath): string {
    $blob = self::read($filePath);
    $mime = mime_content_type($filePath);
    if ($mime === 'image/svg' || $mime === 'image/svgz')
      $mime = 'image/svg+xml';
    return 'data:' . $mime . ';base64,' . base64_encode($blob);
  }

  /**
   * Resize.
   */
  public static function resize(
    string $filePath,
    string $resizePath,
    ?int $width = null,
    ?int $height = null,
    bool $keepAspectRatio = true
  ) {
    $manager = new ImageManager(['driver' => 'gd']);
    $manager
      ->make($filePath)
      ->resize($width, $height, function ($constraint) use($keepAspectRatio) {
        if ($keepAspectRatio)
          $constraint->aspectRatio();
      })
      ->save($resizePath);
  }

  /**
   * Is Base64.
   */
  public static function isBase64(string $base64, &$mime = null): bool {
    if (!preg_match('/^data:image\/(\w+);base64,/', $base64, $matches))
      return false;
    $mime = strtolower($matches[1]);
    return true;
  }

  /**
   * Convert Base64 to blob.
   */
  public static function convertBase64ToBlob(string $base64, &$mime = null): string {
    if (!self::isBase64($base64, $mime))
      throw new \RuntimeException('Did not match data URI with image data');
    $blob = base64_decode(substr($base64, strpos($base64, ',') + 1));
    if ($blob === false)
      throw new \RuntimeException('Base64 decode failed');
    return $blob;
  }
}