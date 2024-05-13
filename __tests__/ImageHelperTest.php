<?php
use PHPUnit\Framework\TestCase;
use \X\Util\ImageHelper;
use \X\Util\FileHelper;

const TMPDIR = __DIR__ . '/tmp';
const INDIR = __DIR__ . '/input';
const OUTDIR = __DIR__ . '/output';

final class ImageHelperTest extends TestCase {
  public static function setUpBeforeClass(): void {
    // During testing, files in the input directory are overwritten, so reset the input directory before testing.
    FileHelper::delete(TMPDIR);
    FileHelper::copyDirectory(INDIR, TMPDIR);
  }

  public function testWriteFirstFrameOfGifInASeparateFile(): void {
    $src = TMPDIR . '/animated.gif';
    $dest = OUTDIR .  '/first-frame-of-gif.gif';
    ImageHelper::extractFirstFrameOfGif($src, $dest);
    $this->assertSame(ImageHelper::getNumberOfGifFrames($dest), 1);
  }

  public function testWriteFirstFrameOfGifInSameFile(): void {
    $src = TMPDIR . '/animated.gif';
    ImageHelper::extractFirstFrameOfGif($src);
    $this->assertSame(ImageHelper::getNumberOfGifFrames($src), 1);
  }

  public function testGetNumberOfFramesInGif(): void {
    $src = TMPDIR . '/animated2.gif';
    $this->assertSame(ImageHelper::getNumberOfGifFrames($src), 19);
  }

  public function testGetNumberOfFramesOfAGifWithoutAnimation(): void {
    $src = TMPDIR . '/non-animated.gif';
    $this->assertSame(ImageHelper::getNumberOfGifFrames($src), 1);
  }

  public function testWriteAllPagesOfPdfAsImage(): void {
    $src = TMPDIR . '/sample.pdf';
    $dest = OUTDIR .  '/pdf.jpg';
    ImageHelper::pdf2Image($src, $dest);
    $this->assertSame(true, true);
  }

  public function testWriteOnlyFirstPageOfPdfAsmage(): void {
    $src = TMPDIR . '/sample.pdf';
    $dest = OUTDIR .  '/pdf.jpg';
    ImageHelper::pdf2Image($src, $dest, ['pageNumber' => 0]);
    $this->assertSame(true, true);
  }
}