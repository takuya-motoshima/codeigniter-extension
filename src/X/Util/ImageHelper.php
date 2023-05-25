<?php
namespace X\Util;
use \X\Util\FileHelper;
use \X\Util\Logger;
use \Intervention\Image\ImageManager;

final class ImageHelper {
  /**
   * Put base64 image.
   * ```php
   * use \X\Util\ImageHelper;
   *
   * ImageHelper::putBase64('data:image/png;base64,iVBOR...', '/tmp', 'sample');
   * ImageHelper::putBase64('data:image/png;base64,iVBOR...', '/tmp/sample.png');
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
   * Put blob image.
   */
  public static function putBlob(string $blob, string $outputPath) {
    FileHelper::makeDirectory(dirname($outputPath));
    file_put_contents($outputPath, $blob, LOCK_EX);
  }

  /**
   * Copy image.
   * ```php
   * // /tmp/example.png -> /home/example.png
   * \X\Util\ImageHelper::copy('/tmp/example.png', '/home');
   *
   * // /tmp/old.png -> /home/new.png
   * \X\Util\ImageHelper::copy('/tmp/old.png', '/home', 'new');
   * ```
   */
  public static function copy(string $inputPath, string $outputDir, string $outputName = null): string {
    FileHelper::makeDirectory($outputDir);
    $outputName = empty($outputName) ? basename($inputPath) : $outputName . '.' . pathinfo($inputPath, PATHINFO_EXTENSION);
    file_put_contents(rtrim($outputDir, '/')  . '/' . $outputName, file_get_contents($inputPath), LOCK_EX);
    return $outputName;
  }

  /**
   * Read image.
   */
  public static function read(string $inputPath): string {
    if (!file_exists($inputPath))
      throw new \RuntimeException('Image file does not exist. Path=' . $inputPath);
    $fp = fopen($inputPath, 'r');
    $blob = fread($fp, filesize($inputPath));
    fclose($fp);
    return $blob;
  }

  /**
   * Read image.
   */
  public static function readAsBase64(string $inputPath): string {
    $blob = self::read($inputPath);
    $mime = mime_content_type($inputPath);
    if ($mime === 'image/svg' || $mime === 'image/svgz')
      $mime = 'image/svg+xml';
    return 'data:' . $mime . ';base64,' . base64_encode($blob);
  }

  /**
   * Resize.
   */
  public static function resize(
    string $inputPath,
    string $outputPath,
    ?int $width = null,
    ?int $height = null,
    bool $keepAspectRatio = true
  ) {
    $manager = new ImageManager(['driver' => 'gd']);
    $manager
      ->make($inputPath)
      ->resize($width, $height, function ($constraint) use($keepAspectRatio) {
        if ($keepAspectRatio)
          $constraint->aspectRatio();
      })
      ->save($outputPath);
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

  /**
   * Extract and save the first frame of the animated GIF.
   *
   * ```php
   * use \X\Util\ImageHelper;
   *
   * // Write the first frame of sample.gif to sample_0.gif.
   * ImageHelper::extractFirstFrameOfGif('sample.gif', 'sample_0.gif');
   *
   * // Overwrite sample.gif with the first frame.
   * ImageHelper::extractFirstFrameOfGif('sample.gif');
   * ```
   */
  public static function extractFirstFrameOfGif(string $inputPath, ?string $outputPath = null) {
    if (!file_exists($inputPath))
      throw new \RuntimeException('Not found file ' . $inputPath);

    // If the output path is unspecified, overwrite it.
    if (empty($outputPath))
      $outputPath = $inputPath;
    $im = new \Imagick($inputPath);
    $written = false;
    if ($im->getNumberImages() > 1) {
      // Write the first frame as an image.
      $im = $im->coalesceImages();
      $im->setIteratorIndex(0);
      $im->writeImage($outputPath);
      $written = true;
    } else if ($outputPath !== $inputPath) {
      FileHelper::copyFile($inputPath, $outputPath);
      $written = true;
    }

    // The owner of the output destination is the same as the original file.
    if ($written) {
      chown($outputPath, fileowner($inputPath));
      chgrp($outputPath, filegroup($inputPath));
    }

    // Destroy resources.
    $im->clear();
  }

  /**
   * Get the number of GIF frames.
   */
  public static function getNumberOfGifFrames(string $inputPath): int {
    if (!file_exists($inputPath))
      throw new \RuntimeException('Not found file ' . $inputPath);
    $im = new \Imagick($inputPath);
    $numberOfFrames = $im->getNumberImages();
    $im->clear();
    return $numberOfFrames;
  }

  /**
   * Convert PDF to image.
   */
  public static function pdf2Image(string $inputPath, string $outputPath, array $options = []): void {
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
        $im->readImage($inputPath);

        // Get the number of pages.
        $numberOfPages = $im->getNumberImages(); 
      } else
        // Only the specified page.
        $im->readImage($inputPath . '[' . $options['pageNumber'] . ']');

      // Writes an image.
      $im->writeImages($outputPath, false);

      // Destroy resources.
      $im->clear();

      // Resize the written image.
      if (!empty($options['width']) || !empty($options['height'])) {
        if ($isWriteAllPages) {
          for ($i=0; $i<$numberOfPages; $i++) {
            $path = preg_replace('/\.(..*)$/', "-{$i}.$1", $outputPath);
            self::resize($path, $path, $options['width'], $options['height']);
          }
        } else
          self::resize($outputPath, $outputPath, $options['width'], $options['height']);
      }
    } catch (\Throwable $e) {
      Logger::error("Error in {$inputPath}'s PDF conversion");
      throw $e;
    }
  }
}