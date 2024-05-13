<?php
namespace X\Util;
use \X\Util\FileHelper;
use \X\Util\Logger;
use \Intervention\Image\ImageManager;

/**
 * Image utility.
 */
final class ImageHelper {
  /**
   * Write data URL to a file.
   * ```php
   * use \X\Util\ImageHelper;
   *
   * ImageHelper::writeDataURLToFile('data:image/png;base64,iVBOR...', '/tmp', 'sample');
   * ImageHelper::writeDataURLToFile('data:image/png;base64,iVBOR...', '/tmp/sample.png');
   * ```
    * @param string $dataURL Image Data URL.
    * @param string $dir Destination directory or file path. For file paths, the $filename parameter is not required.
    * @param string|null $filename (optional) File Name. If the file name does not have an extension, the guessed extension is automatically assigned.
    * @return string Name of the output file.
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
   * Write blob to a file.
   * @param string $blob Image Blob.
   * @param string $filePath Output file path.
   * @return void
   */
  public static function writeBlobToFile(string $blob, string $filePath): void {
    FileHelper::makeDirectory(dirname($filePath));
    file_put_contents($filePath, $blob, LOCK_EX);
  }

  /**
   * Copy image.
   * ```php
   * use \X\Util\ImageHelper;
   * 
   * ImageHelper::copy('/tmp/example.png', '/home');// => /tmp/example.png -> /home/example.png
   * ImageHelper::copy('/tmp/old.png', '/home', 'new');// => /tmp/old.png -> /home/new.png
   * ```
   * @param string $src Source file path.
   * @param string $dir Destination directory path.
   * @param string|null $filename (optional) Destination file name. If omitted, the source file name in $src becomes the destination file name.
   * @return string Output file name.
   */
  public static function copy(string $src, string $dir, string $filename=null): string {
    FileHelper::makeDirectory($dir);
    $filename = empty($filename)
      ? basename($src)
      : $filename . '.' . pathinfo($src, PATHINFO_EXTENSION);
    file_put_contents(rtrim($dir, '/')  . '/' . $filename, file_get_contents($src), LOCK_EX);
    return $filename;
  }

  /**
   * Get the contents of the media file as a blob string.
   * @param string $filePath File path.
   * @return string Blob.
   */
  public static function readAsBlob(string $filePath): string {
    if (!file_exists($filePath))
      throw new \RuntimeException('Image file does not exist. Path=' . $filePath);
    $fp = fopen($filePath, 'r');
    $blob = fread($fp, filesize($filePath));
    fclose($fp);
    return $blob;
  }

  /**
   * Get the contents of the media file as a data URL string.
   * @param string $filePath File path.
   * @return string Data URL.
   */
  public static function readAsDataURL(string $filePath): string {
    $blob = self::readAsBlob($filePath);
    $mime = mime_content_type($filePath);
    if ($mime === 'image/svg' || $mime === 'image/svgz')
      $mime = 'image/svg+xml';
    return 'data:' . $mime . ';base64,' . base64_encode($blob);
  }

  /**
   * Resize the image.
   * @param string $src Input file path.
   * @param string $dest Output file path.
   * @param int|null $width (optional) Width (pixels) after resizing. If omitted, adjust to height.
   * @param int|null $height (optional) Height after resizing (pixels). If omitted, matches the width.
   * @param bool $keepAspectRatio (optional) Whether the aspect ratio of the original image is maintained after resizing. Default is true.
   * @return void
   */
  public static function resize(string $src, string $dest, ?int $width=null, ?int $height=null, bool $keepAspectRatio=true): void {
    $manager = new ImageManager(['driver' => 'gd']);
    $manager
      ->make($src)
      ->resize($width, $height, function ($constraint) use($keepAspectRatio) {
        if ($keepAspectRatio)
          $constraint->aspectRatio();
      })
      ->save($dest);
  }

  /**
   * Check if it is a Data URL.
   * @param string $dataURL String.
   * @param string &$mime (optional) If specified, the MIME type detected from the Data URL is set.
   * @return bool Data URL or not.
   */
  public static function isDataURL(string $dataURL, &$mime=null): bool {
    if (!preg_match('/^data:image\/(\w+);base64,/', $dataURL, $matches))
      return false;
    $mime = strtolower($matches[1]);
    return true;
  }

  /**
   * Convert Data URL to blob.
   * @param string $dataURL String.
   * @param string &$mime (optional) If specified, the MIME type detected from the Data URL is set.
   * @return string Blob.
   */
  public static function dataURL2Blob(string $dataURL, &$mime=null): string {
    if (!self::isDataURL($dataURL, $mime))
      throw new \RuntimeException('Did not match data URI with image data');
    $blob = base64_decode(substr($dataURL, strpos($dataURL, ',') + 1));
    if ($blob === false)
      throw new \RuntimeException('Base64 decode failed');
    return $blob;
  }

  /**
   * Extract and save the first frame of the animated GIF.
   * ```php
   * use \X\Util\ImageHelper;
   *
   * // Write the first frame of sample.gif to sample_0.gif.
   * ImageHelper::extractFirstFrameOfGif('sample.gif', 'sample_0.gif');
   *
   * // Overwrite sample.gif with the first frame.
   * ImageHelper::extractFirstFrameOfGif('sample.gif');
   * ```
   * @param string $src Input file path.
   * @param string|null $dest (optional) Output file path. If omitted, the input file is overwritten.
   * @return void
   */
  public static function extractFirstFrameOfGif(string $src, ?string $dest=null): void {
    if (!file_exists($src))
      throw new \RuntimeException('Not found file ' . $src);
    if (empty($dest))
      // If the output path is unspecified, overwrite it.
      $dest = $src;
    $im = new \Imagick($src);
    $written = false;
    if ($im->getNumberImages() > 1) {
      // Write the first frame as an image.
      $im = $im->coalesceImages();
      $im->setIteratorIndex(0);
      $im->writeImage($dest);
      $written = true;
    } else if ($dest !== $src) {
      FileHelper::copyFile($src, $dest);
      $written = true;
    }
    if ($written) {
      // The owner of the output destination is the same as the original file.
      chown($dest, fileowner($src));
      chgrp($dest, filegroup($src));
    }

    // Destroy resources.
    $im->clear();
  }

  /**
   * Get the number of GIF frames.
   * @param string $filePath Input file path.
   * @return int Number of GIF frames.
   */
  public static function getNumberOfGifFrames(string $filePath): int {
    if (!file_exists($filePath))
      throw new \RuntimeException('Not found file ' . $filePath);
    $im = new \Imagick($filePath);
    $frameCount = $im->getNumberImages();
    $im->clear();
    return $frameCount;
  }

  /**
   * Convert PDF to image.
   * @param string $src Input file path.
   * @param string $dest Output file path.
   * @param int|null $options[pageNumber] (optional) Page number to out. Default is null, which outputs all pages. Offset is zero.
   * @param int $options[xResolution] (optional) The horizontal resolution. Default is 288.
   * @param int $options[yResolution] (optional) The vertical resolution. Default is 288.
   * @param int $options[width] (optional) Resize width. Default is no resizing (null).
   * @param int $options[height] (optional) Resize Height. Default is no resizing (null).
   * @return void
   */
  public static function pdf2Image(string $src, string $dest, array $options=[]): void {
    try {
      // Initialize options.
      $options = array_merge([
        'pageNumber' => null,// Page number to out. Default is null, which outputs all pages. Offset is zero.
        'xResolution' => 288,// The horizontal resolution. Default is 288.
        'yResolution' => 288,// The vertical resolution. Default is 288.
        'width' => null,// Resize width. Default is no resizing (null).
        'height' => null,// Resize Height. Default is no resizing (null).
      ], $options);

      // Imagick instance.
      $im = new \Imagick();

      // Specify the resolution.
      $im->setResolution($options['xResolution'], $options['yResolution']);

      // Write all pages?
      $isWriteAllPages = !isset($options['pageNumber']);

      // Reads image from PDF.
      if ($isWriteAllPages) {
        // All pages.
        $im->readImage($src);

        // Get the number of pages.
        $pageCount = $im->getNumberImages(); 
      } else
        // Only the specified page.
        $im->readImage($src . '[' . $options['pageNumber'] . ']');

      // Writes an image.
      $im->writeImages($dest, false);

      // Destroy resources.
      $im->clear();

      // Resize the written image.
      if (!empty($options['width']) || !empty($options['height'])) {
        if ($isWriteAllPages) {
          for ($i=0; $i<$pageCount; $i++) {
            $path = preg_replace('/\.(..*)$/', "-{$i}.$1", $dest);
            self::resize($path, $path, $options['width'], $options['height']);
          }
        } else
          self::resize($dest, $dest, $options['width'], $options['height']);
      }
    } catch (\Throwable $e) {
      Logger::error("Error in {$src}'s PDF conversion");
      throw $e;
    }
  }
}