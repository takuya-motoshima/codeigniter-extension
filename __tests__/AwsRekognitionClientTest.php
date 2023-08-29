<?php
use PHPUnit\Framework\TestCase;
use \X\Util\ImageHelper;
use \X\Util\FileHelper;
use \X\Rekognition\Client;

final class AwsRekognitionClientTest extends TestCase {
  const INPUT_TMP_DIR = __DIR__ . '/tmp';
  const INPUT_DIR = __DIR__ . '/input';

  /**
   * An instance of AWS Rekognition Client.
   *
   * @var \X\Rekognition\Client
   */
  private $client;

  public static function setUpBeforeClass(): void {
    // During testing, files in the input directory are overwritten, so reset the input directory before testing.
    FileHelper::delete(self::INPUT_TMP_DIR);
    FileHelper::copyDirectory(self::INPUT_DIR, self::INPUT_TMP_DIR);
  }

  protected function setUp(): void {
    // Load environment variables.
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv ->load();

    // An instance of AWS Rekognition Client.
    $this->client = new Client([
      'key' => $_ENV['AWS_REKOGNITION_ACCESS_KEY'],
      'secret' => $_ENV['AWS_REKOGNITION_SECRET_KEY'],
    ]);
  }


  public function testFacesOfSamePersonMatch(): void {
    $similarity = $this->client->compareFaces(
      self::INPUT_TMP_DIR . '/person-1-face-1.jpg',
      self::INPUT_TMP_DIR . '/person-1-face-2.jpg'
    );
    $this->assertTrue($similarity > 90);
  }

  public function testFacesOfDifferentPeopleDoNotMatch(): void {
    $similarity = $this->client->compareFaces(
      self::INPUT_TMP_DIR . '/person-1-face-1.jpg',
      self::INPUT_TMP_DIR . '/person-2-face-1.jpg'
    );
    $this->assertTrue($similarity < 1);
  }

  public function testZeroSimilarityForImagesWithoutFace(): void {
    // $this->expectException(RuntimeException::class);
    $similarity = $this->client->compareFaces(
      self::INPUT_TMP_DIR . '/person-1-face-1.jpg',
      self::INPUT_TMP_DIR . '/face-not-found.jpg'
    );
    $this->assertEquals($similarity, 0);
  }
}