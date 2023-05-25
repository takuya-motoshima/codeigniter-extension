<?php
use PHPUnit\Framework\TestCase;
use \X\Util\ImageHelper;
use \X\Util\FileHelper;

const INPUT_DIR = __DIR__ . '/input';
const INPUT_BAK_DIR = __DIR__ . '/input-backup';
const OUTPUT_DIR = __DIR__ . '/output';

final class ImageHelperTest extends TestCase {
  public static function setUpBeforeClass(): void {
    // During testing, files in the input directory are overwritten, so reset the input directory before testing.
    FileHelper::delete(INPUT_DIR);
    FileHelper::copyDirectory(INPUT_BAK_DIR, INPUT_DIR);
  }

  // public function testWriteFirstFrameOfGifInSeparateFile(): void {
  //   $inputPath = INPUT_DIR . '/sample.gif';
  //   $outputPath = OUTPUT_DIR .  '/write-first-frame-of-gif-in-separate-file.gif';
  //   ImageHelper::extractFirstFrameOfGif($inputPath, $outputPath);
  //   $this->assertSame(ImageHelper::getNumberOfGifFrames($outputPath), 1);
  // }

  // public function testWriteFirstFrameOfGifInOriginalFile(): void {
  //   $inputPath = INPUT_DIR . '/sample.gif';
  //   ImageHelper::extractFirstFrameOfGif($inputPath);
  //   $this->assertSame(ImageHelper::getNumberOfGifFrames($inputPath), 1);
  // }

  // public function testGetNumberOfGifFrames(): void {
  //   $inputPath = INPUT_DIR . '/sample_2.gif';
  //   $this->assertSame(ImageHelper::getNumberOfGifFrames($inputPath), 19);
  // }

  // public function testGetNumberOfFramesInGifWithNoAnimation(): void {
  //   $inputPath = INPUT_DIR . '/non-animated.gif';
  //   $this->assertSame(ImageHelper::getNumberOfGifFrames($inputPath), 1);
  // }

  public function testConvertAllPagesOfPdfToJpg(): void {
    $inputPath = INPUT_DIR . '/sample.pdf';
    $outputPath = OUTPUT_DIR .  '/pdf.jpg';
    ImageHelper::pdf2Image($inputPath, $outputPath);
    $this->assertSame(true, true);
  }

  public function testConvertOnlyOnePageOfPdfToJpg(): void {
    $inputPath = INPUT_DIR . '/sample.pdf';
    $outputPath = OUTPUT_DIR .  '/pdf.jpg';
    ImageHelper::pdf2Image($inputPath, $outputPath, ['pageNumber' => 0]);
    $this->assertSame(true, true);
  }
}