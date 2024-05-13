<?php
use PHPUnit\Framework\TestCase;
use \X\Util\FileHelper;

const TMPDIR = __DIR__ . '/tmp';
const INDIR = __DIR__ . '/input';

final class FileHelperTest extends TestCase {
  public static function setUpBeforeClass(): void {
    // During testing, files in the input directory are overwritten, so reset the input directory before testing.
    FileHelper::delete(TMPDIR);
    FileHelper::copyDirectory(INDIR, TMPDIR);
  }

  public function testDeleteDirectoriesRecursively(): void {
    $deleteSelf = true;
    $dir = TMPDIR . '/recursively-delete';
    FileHelper::delete($dir, $deleteSelf);
    $this->assertSame(file_exists($dir), false);
  }

  public function testDeleteRecursivelyOnlyChildrenOfDirectory(): void {
    $deleteSelf = false;
    $dir = TMPDIR . '/recursively-delete-only-children';
    FileHelper::delete($dir, $deleteSelf);
    $fileCount = count(glob($dir . '/*'));
    $this->assertSame($fileCount, 0);
  }

  public function testMakeDirector(): void {
    $dir = TMPDIR . '/path';
    FileHelper::makeDirectory($dir);
    $directoryExists = file_exists($dir);
    $this->assertSame($directoryExists, true);
  }

  public function testMakeDirectorAlreadyExists(): void {
    $dir = TMPDIR . '/path';
    FileHelper::makeDirectory($dir);
    $directoryExists = file_exists($dir);
    $this->assertSame($directoryExists, true);
  }
}