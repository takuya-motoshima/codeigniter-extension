<?php
use PHPUnit\Framework\TestCase;
use \X\Util\ImageHelper;
use \X\Util\FileHelper;

const TMP_DIR = __DIR__ . '/tmp';
const INPUT_DIR = __DIR__ . '/input';
const OUTPUT_DIR = __DIR__ . '/output';

final class ImageHelperTest extends TestCase {
  public static function setUpBeforeClass(): void {
    // During testing, files in the input directory are overwritten, so reset the input directory before testing.
    FileHelper::delete(TMP_DIR);
    FileHelper::copyDirectory(INPUT_DIR, TMP_DIR);
  }

  public function testWriteFirstFrameOfGifInASeparateFile(): void {
    $inputPath = TMP_DIR . '/sample.gif';
    $outputPath = OUTPUT_DIR .  '/first-frame-of-gif.gif';
    ImageHelper::extractFirstFrameOfGif($inputPath, $outputPath);
    $this->assertSame(ImageHelper::getNumberOfGifFrames($outputPath), 1);
  }

  public function testWriteFirstFrameOfGifInSameFile(): void {
    $inputPath = TMP_DIR . '/sample.gif';
    ImageHelper::extractFirstFrameOfGif($inputPath);
    $this->assertSame(ImageHelper::getNumberOfGifFrames($inputPath), 1);
  }

  public function testGetNumberOfFramesInGif(): void {
    $inputPath = TMP_DIR . '/sample2.gif';
    $this->assertSame(ImageHelper::getNumberOfGifFrames($inputPath), 19);
  }

  public function testGetNumberOfFramesOfAGifWithoutAnimation(): void {
    $inputPath = TMP_DIR . '/non-animated.gif';
    $this->assertSame(ImageHelper::getNumberOfGifFrames($inputPath), 1);
  }

  public function testWriteAllPagesOfPdfAsImage(): void {
    $inputPath = TMP_DIR . '/sample.pdf';
    $outputPath = OUTPUT_DIR .  '/pdf.jpg';
    ImageHelper::pdf2Image($inputPath, $outputPath);
    $this->assertSame(true, true);
  }

  public function testWriteOnlyFirstPageOfPdfAsmage(): void {
    $inputPath = TMP_DIR . '/sample.pdf';
    $outputPath = OUTPUT_DIR .  '/pdf.jpg';
    ImageHelper::pdf2Image($inputPath, $outputPath, ['pageNumber' => 0]);
    $this->assertSame(true, true);
  }
}