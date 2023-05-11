<?php
use PHPUnit\Framework\TestCase;
use \X\Util\ImageHelper;
use \X\Util\FileHelper;
use \X\Rekognition\Client;

final class AwsRekognitionClientTest extends TestCase {
  const INPUT_DIR = __DIR__ . '/input';
  const INPUT_BAK_DIR = __DIR__ . '/input-backup';

  /**
   * An instance of AWS Rekognition Client.
   *
   * @var \X\Rekognition\Client
   */
  private $client;

  public static function setUpBeforeClass(): void {
    // During testing, files in the input directory are overwritten, so reset the input directory before testing.
    FileHelper::delete(self::INPUT_DIR);
    FileHelper::copyDirectory(self::INPUT_BAK_DIR, self::INPUT_DIR);
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

  public function testTwoFacesShouldBeTheSame(): void {
    $face1 = self::INPUT_DIR . '/face_1.jpg';
    $face2 = self::INPUT_DIR . '/face_3.jpg';
    $similarity = $this->client->compareFaces($face1, $face2);
    $this->assertTrue($similarity >= 0);
  }

  public function testTwoFacesShouldBeDifferent(): void {
    $face1 = self::INPUT_DIR . '/face_1.jpg';
    $face2 = self::INPUT_DIR . '/face_2.jpg';
    $similarity = $this->client->compareFaces($face1, $face2);
    $this->assertTrue($similarity >= 0);
  }

  public function testComparisonsOtherThanFacialShouldBeAnError(): void {
    $this->expectException(RuntimeException::class);
    $face = self::INPUT_DIR . '/face_1.jpg';
    $car = self::INPUT_DIR . '/car.jpg';
    $this->client->compareFaces($face, $car);
  }
}