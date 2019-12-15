<?php
/**
 * Amazon rekognition client
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Rekognition;

use \Aws\Rekognition\RekognitionClient;
use \Aws\Rekognition\Exception\RekognitionException;
use \X\Util\ImageHelper;
use \X\Util\Logger;

class DetectClient {

  private $rekognition;
  private $debug;

  /**
   * 
   * construct
   *
   * @param string       $key
   * @param string       $secret
   * @param bool|boolean $debug
   */
  public function __construct(string $key, string $secret, bool $debug = false) {
    $this->rekognition = new RekognitionClient([
      'region' => 'ap-northeast-1',
      'version' => 'latest',
      'credentials' => ['key' => $key, 'secret' => $secret]
    ]);
    $this->debug = $debug;
  }

  /**
   * 
   * Compare face
   * 
   * @param  string      $base64Image1 Image binary data
   * @param  string      $base64Image2 Image binary data
   * @param  int $threshold
   * @return bool
   */
  public function compare(string $base64Image1, string $base64Image2, int $threshold = 80): bool {
    try {
      $res = $this->rekognition
        ->compareFaces([
          'SimilarityThreshold' => $threshold,
          'SourceImage' => [
            'Bytes' => ImageHelper::isBase64($base64Image1) ? ImageHelper::convertBase64ToBlob($base64Image1) : $base64Image1
          ],
          'TargetImage' => [
            'Bytes' => ImageHelper::isBase64($base64Image2) ? ImageHelper::convertBase64ToBlob($base64Image2) : $base64Image2
          ]
        ])
        ->toArray();
      $this->debug && Logger::debug('Face comparison results: ', $res);
      $status = !empty($res['@metadata']['statusCode']) ? (int) $res['@metadata']['statusCode'] : null;
      if ($status !== 200) {
        throw new \RuntimeException('Face comparison error');
      }
      return $res['FaceMatches'] ? true : false;
    } catch (RekognitionException $e) {
      Logger::error($e);
      throw $e;
    } catch (Throwable $e) {
      Logger::error($e);
      throw $e;
    }
  }

  /**
   * 
   * Compare face by path
   * 
   * @param  string      $imagePath1 Image path
   * @param  string      $imagePath2 Image path
   * @param  int $threshold
   * @return bool
   */
  public function compareFromPath(string $imagePath1, string $imagePath2, int $threshold = 80): bool {
    return $this->compare(ImageHelper::read($imagePath1), ImageHelper::read($imagePath2), $threshold);
  }

  /**
   * 
   * Detect face
   *
   * @param string $base64Image Image binary data
   * @param int $threshold
   * @return array
   */
  public function detect(string $base64Image, int $threshold = 90): array {
    try {
      $res = $this->rekognition
        ->DetectFaces([
          'Image' => [
            'Bytes' => ImageHelper::isBase64($base64Image) ? ImageHelper::convertBase64ToBlob($base64Image) : $base64Image
          ],
          'Attributes' => ['DEFAULT']
        ])
        ->toArray();
      $this->debug && Logger::debug('Face detection result: ', $res); 
      $status = !empty($res['@metadata']['statusCode']) ? (int) $res['@metadata']['statusCode'] : null;
      if ($status !== 200) throw new \RuntimeException('Face detection error');
      if (empty($res['FaceDetails'])) return [];
      $faces = $res['FaceDetails'];
      return array_filter($res['FaceDetails'], function(array $face) use($threshold) {
        return $face['Confidence'] >= $threshold;
      });
    } catch (Throwable $e) {
      Logger::error($e);
      throw $e;
    }
  }

  /**
   * 
   * Is face by path
   *
   * @param string $imagePath Image path
   * @param int $threshold
   * @return array
   */
  public function detectFromPath(string $imagePath, int $threshold = 90): array {
    return $this->detect(ImageHelper::read($imagePath), $threshold);
  }

  /**
   * 
   * Count face
   *
   * @param string $base64Image Image binary data
   * @param int $threshold
   * @return int
   */
  public function count(string $base64Image, int $threshold = 90): int {
    return count($this->detect($base64Image, $threshold));
  }

  /**
   * 
   * Count face by path
   *
   * @param string $imagePath Image path
   * @param int $threshold
   * @return int
   */
  public function countFromPath(string $imagePath, int $threshold = 90): int {
    return count($this->detectFromPath($imagePath, $threshold));
  }
}