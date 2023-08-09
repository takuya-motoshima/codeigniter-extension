<?php
use PHPUnit\Framework\TestCase;
use \X\Util\FileHelper;

const TMP_DIR = __DIR__ . '/tmp';
const INPUT_DIR = __DIR__ . '/input';

final class FileHelperTest extends TestCase {
  public static function setUpBeforeClass(): void {
    // During testing, files in the input directory are overwritten, so reset the input directory before testing.
    FileHelper::delete(TMP_DIR);
    FileHelper::copyDirectory(INPUT_DIR, TMP_DIR);
  }

  public function testDeleteDirectoriesRecursively(): void {
    $deleteSelf = true;
    $dir = TMP_DIR . '/recursively-delete';
    FileHelper::delete($dir, $deleteSelf);
    $this->assertSame(file_exists($dir), false);
  }

  public function testDeleteRecursivelyOnlyChildrenOfDirectory(): void {
    $deleteSelf = false;
    $dir = TMP_DIR . '/recursively-delete-only-children';
    FileHelper::delete($dir, $deleteSelf);
    $numberOfFiles = count(glob($dir . '/*'));
    $this->assertSame($numberOfFiles, 0);
  }
}