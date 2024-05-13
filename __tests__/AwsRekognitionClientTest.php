<?php
use PHPUnit\Framework\TestCase;
use \X\Util\ImageHelper;
use \X\Util\FileHelper;
use \X\Rekognition\Client;

final class AwsRekognitionClientTest extends TestCase {
  const TMPDIR = __DIR__ . '/tmp';
  const INDIR = __DIR__ . '/input';

  /**
   * An instance of AWS Rekognition Client.
   * @var \X\Rekognition\Client
   */
  private $client;

  public static function setUpBeforeClass(): void {
    // During testing, files in the input directory are overwritten, so reset the input directory before testing.
    FileHelper::delete(self::TMPDIR);
    FileHelper::copyDirectory(self::INDIR, self::TMPDIR);
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
      self::TMPDIR . '/person1_1.jpg',
      self::TMPDIR . '/person1_2.jpg'
    );
    $this->assertTrue($similarity > 90);
  }

  public function testFacesOfDifferentPeopleDoNotMatch(): void {
    $similarity = $this->client->compareFaces(
      self::TMPDIR . '/person1_1.jpg',
      self::TMPDIR . '/person2.jpg'
    );
    $this->assertTrue($similarity < 10);
  }

  public function testZeroSimilarityForImagesWithoutFace(): void {
    // $this->expectException(RuntimeException::class);
    $similarity = $this->client->compareFaces(
      self::TMPDIR . '/person1_1.jpg',
      self::TMPDIR . '/face-not-found.jpg'
    );
    $this->assertEquals($similarity, 0);
  }
}