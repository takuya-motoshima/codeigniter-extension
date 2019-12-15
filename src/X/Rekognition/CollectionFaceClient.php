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
use \X\Rekognition\DetectClient;
use \X\Util\ImageHelper;
use \X\Util\Logger;

class CollectionFaceClient {

  private $rekognition;
  private $detect;
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
    $this->detect = new DetectClient($key, $secret, $debug);
    $this->rekognition = new RekognitionClient([
      'region' => 'ap-northeast-1',
      'version' => 'latest',
      'credentials' => ['key' => $key, 'secret' => $secret]
    ]);
    $this->debug = $debug;
  }

  /**
   * 
   * Add face to coolection
   *
   * @param string $collectionId
   * @param string $base64Image  Image binary data
   * @return bool
   */
  public function add(string $collectionId, string $base64Image): string {
    try {
      if (ImageHelper::isBase64($base64Image)) $base64Image = ImageHelper::convertBase64ToBlob($base64Image);
      $count = $this->detect->count($base64Image);
      if ($count === 0) throw new \RuntimeException('Face not detected');
      if ($count > 1) throw new \RuntimeException('Multiple faces can not be registered');
      $res = $this->rekognition
        ->indexFaces([
          'CollectionId' => $collectionId,
          'DetectionAttributes' => ['ALL'],
          // 'ExternalImageId' => '',
          'Image' => ['Bytes' => $base64Image],
        ])
        ->toArray();
      $this->debug && Logger::debug('Face creation result: ', $res);
      $status = !empty($res['@metadata']['statusCode']) ? (int) $res['@metadata']['statusCode'] : null;
      if ($status !== 200) throw new \RuntimeException('Collection face registration error');
      if (empty($res['FaceRecords'])) throw new \RuntimeException('This image does not include faces');
      return $res['FaceRecords'][0]['Face']['FaceId'];
    } catch (Throwable $e) {
      Logger::error($e);
      throw $e;
    }
  }

  /**
   * 
   * Get faces from collection
   *
   * @param string $collectionId
   * @return bool
   */
  public function getAll(string $collectionId): array {
    try {
      $res = $this->rekognition
        ->listFaces(['CollectionId' => $collectionId, 'MaxResults' => 4096,])
        ->toArray();
      $this->debug && Logger::debug('All face search results: ', $res);
      $status = !empty($res['@metadata']['statusCode']) ? (int) $res['@metadata']['statusCode'] : null;
      if ($status !== 200) throw new \RuntimeException('Collection face list acquisition error');
      return $res['Faces'];
    } catch (Throwable $e) {
      Logger::error($e);
      throw $e;
    }
  }

  /**
   * 
   * Match face from collection
   *
   * @param  string $collectionId
   * @param  string $base64Image
   * @return bool
   */
  public function get(string $collectionId, string $base64Image, ?string &$faceId = null, int $threshold = 95): bool {
    try {
      if (ImageHelper::isBase64($base64Image)) $base64Image = ImageHelper::convertBase64ToBlob($base64Image);
      $count = $this->detect->count($base64Image);
      if ($count === 0) throw new \RuntimeException('Face not detected');
      if ($count > 1) throw new \RuntimeException('Multiple faces can not be matched');
      $res = $this->rekognition
        ->searchFacesByImage([
          'CollectionId' => $collectionId,
          'FaceMatchThreshold' => $threshold,
          'Image' => ['Bytes' => $base64Image],
          'MaxFaces' => 1,
        ])
        ->toArray();
      $this->debug && Logger::debug('Face search results: ', $res);
      $status = !empty($res['@metadata']['statusCode']) ? (int) $res['@metadata']['statusCode'] : null;
      if ($status !== 200) throw new \RuntimeException('Collection getting error');
      if (empty($res['FaceMatches'])) return false;
      $faceId = $res['FaceMatches'][0]['Face']['FaceId'];
      return true;
    } catch (Throwable $e) {
      Logger::error($e);
      throw $e;
    }
  }

  /**
   * Delete image from collection
   *
   * @param string $collectionId
   * @param  array $faceIds
   * @return array
   */
  public function delete(string $collectionId, array $faceIds): array {
    try {
      $res = $this->rekognition
        ->deleteFaces(['CollectionId' => $collectionId, 'FaceIds' => $faceIds])
        ->toArray();
      $this->debug && Logger::debug('Face removal result: ', $res);
      $status = !empty($res['@metadata']['statusCode']) ? (int) $res['@metadata']['statusCode'] : null;
      if ($status !== 200) throw new \RuntimeException('Collection face deletion error');
      $faces = $res['DeletedFaces'] ?? [];
      return $faces;
    } catch (RekognitionException $e) {
      Logger::error($e);
      throw $e;
    } catch (Throwable $e) {
      Logger::error($e);
      throw $e;
    }
  }
}