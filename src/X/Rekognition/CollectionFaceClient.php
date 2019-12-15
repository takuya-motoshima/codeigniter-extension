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

  protected $client;

  /**
   * 
   * construct
   *
   * @param array $config
   */
  public function __construct(array $config = []) {
    $this->detectClient = new DetectClient($config);
    $this->client = new RekognitionClient(array_replace_recursive([
      'region'      => 'ap-northeast-1',
      'version'     => 'latest',
      'credentials' => ['key' => null, 'secret' => null]
    ], $config));
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
      $faceCount = $this->detectClient->count($base64Image);
      if ($faceCount === 0) throw new \RuntimeException('Face not detected');
      if ($faceCount > 1) throw new \RuntimeException('Multiple faces can not be registered');
      $res = $this->client->indexFaces([
        'CollectionId' => $collectionId,
        'DetectionAttributes' => ['ALL'],
        // 'ExternalImageId' => '',
        'Image' => ['Bytes' => $base64Image],
      ]);
      $res = $res->toArray();
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
      $res = $this->client->listFaces(['CollectionId' => $collectionId, 'MaxResults' => 4096,]);
      $res = $res->toArray();
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
  public function match(string $collectionId, string $base64Image, ?string &$faceId = null, int $threshold = 95): bool {
    try {
      if (ImageHelper::isBase64($base64Image)) {
        $base64Image = ImageHelper::convertBase64ToBlob($base64Image);
      }
      $faceCount = $this->detectClient->count($base64Image);
      if ($faceCount === 0) throw new \RuntimeException('Face not detected');
      if ($faceCount > 1) throw new \RuntimeException('Multiple faces can not be matched');
      $res = $this->client->searchFacesByImage([
        'CollectionId' => $collectionId,
        'FaceMatchThreshold' => $threshold,
        'Image' => ['Bytes' => $base64Image],
        'MaxFaces' => 1,
      ]);
      $res = $res->toArray();
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
      $res = $this->client->deleteFaces(['CollectionId' => $collectionId, 'FaceIds' => $faceIds]);
      $res = $res->toArray();
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