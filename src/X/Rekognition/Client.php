<?php

/**
 * Amazon rekognition client
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2020 Takuya Motoshima
 */
namespace X\Rekognition;

use \Aws\Rekognition\RekognitionClient;
use \Aws\Rekognition\Exception\RekognitionException;
use \X\Util\ImageHelper;
use \X\Util\Logger;

class Client {

  private $client;
  private $debug;
 
  /**
   * @param string       $key
   * @param string       $secret
   * @param bool|boolean $debug
   */
  public function __construct(string $key, string $secret, bool $debug = false) {
    $this->client = new RekognitionClient([
      'region' => 'ap-northeast-1',
      'version' => 'latest',
      'credentials' => [
        'key' => $key,
        'secret' => $secret
      ]
    ]);
    $this->debug = $debug;
  }

  /**
   * @param string $collectionId
   * @return void
   */
  public function addCollection(string $collectionId) {
    try {
      $response = $this->client->createCollection(['CollectionId' => $collectionId])->toArray();
      if ($this->debug) {
        Logger::debug('Response: ', $response);
      }
      $status = $response['StatusCode'] ?? null;
      if ($status != 200) {
        throw new \RuntimeException('Collection could not be created');
      }
    } catch (RekognitionException $e) {
      if ($e->getAwsErrorCode() !== 'ResourceAlreadyExistsException') {
        throw $e;
      }
    }
  }

  /**
   * @param string $collectionId
   * @return void
   */
  public function getCollection(string $collectionId): ?array {
    try {
      $response = $this->client->describeCollection(['CollectionId' => $collectionId])->toArray();
      if ($this->debug) {
        Logger::debug('Response: ', $response);
      }
      $status = $response['@metadata']['statusCode'] ?? null;
      if ($status != 200) {
        throw new \RuntimeException('Collection getting error');
      }
      return $response;
    } catch (RekognitionException $e) {
      if ($e->getAwsErrorCode() !== 'ResourceNotFoundException') {
        throw $e;
      }
      return null;
    }
  }

  /**
   * @return array
   */
  public function getAllCollections(): array {
    $response = $this->client->listCollections()->toArray();
    if ($this->debug) {
      Logger::debug('Response: ', $response);
    }
    $status = $response['@metadata']['statusCode'] ?? null;
    if ($status != 200) {
      throw new \RuntimeException('Collection getting error');
    }
    return !empty($response['CollectionIds']) ? $response['CollectionIds'] : [];
  }

  /**
   * @param string $collectionId
   * @return void
   */
  public function deleteCollection(string $collectionId) {
    try {
      $response = $this->client->deleteCollection(['CollectionId' => $collectionId])->toArray();
      if ($this->debug) {
        Logger::debug('Response: ', $response);
      }
      $status = $response['StatusCode'] ?? null;
      if ($status != 200) {
        throw new \RuntimeException('Collection could not be delete');
      }
    } catch (RekognitionException $e) {
      if ($e->getAwsErrorCode() !== 'ResourceNotFoundException') {
        throw $e;
      }
    }
  }

  /**
   * @param string $collectionId
   * @return bool
   */
  public function existsCollection(string $collectionId): bool {
    return !empty($this->getCollection($collectionId));
  }

  /**
   * @param string $collectionId
   * @param string $faceImage
   * @return string
   */
  public function addFaceToCollection(string $collectionId, string $faceImage): string {
    if (ImageHelper::isBase64($faceImage)) {
      $faceImage = ImageHelper::convertBase64ToBlob($faceImage);
    }
    $numberOfFaces = $this->getNumberOfFaces($faceImage);
    if ($numberOfFaces === 0) {
      throw new \RuntimeException('Face not detected');
    } else if ($numberOfFaces > 1) {
      throw new \RuntimeException('Multiple faces can not be registered');
    }
    $response = $this->client->indexFaces([
      'CollectionId' => $collectionId,
      'DetectionAttributes' => [ 'ALL' ],
      'Image' => [
        'Bytes' => $faceImage
      ]
    ])->toArray();
    if ($this->debug) {
      Logger::debug('Response: ', $response);
    }
    $status = $response['@metadata']['statusCode'] ?? null;
    if ($status != 200) {
      throw new \RuntimeException('Collection face registration error');
    }
    if (empty($response['FaceRecords'])) {
      throw new \RuntimeException('This image does not include faces');
    }
    return $response['FaceRecords'][0]['Face']['FaceId'];
  }

  /**
   * @param string $collectionId
   * @return array
   */
  public function getAllFacesFromCollection(string $collectionId, int $maxResults = 4096): array {
    $response = $this->client->listFaces(['CollectionId' => $collectionId, 'MaxResults' => $maxResults ])->toArray();
    if ($this->debug) {
      Logger::debug('Response: ', $response);
    }
    $status = $response['@metadata']['statusCode'] ?? null;
    if ($status != 200) {
      throw new \RuntimeException('Collection face list acquisition error');
    }
    return $response['Faces'];
  }

  /**
   * @param  string $collectionId
   * @param  string $faceImage
   * @return array
   */
  public function getFaceFromCollectionByImage(string $collectionId, string $faceImage, int $threshold = 80): ?array {
    if (\preg_match('/^\//', $faceImage) && \is_file($faceImage)) {
      $faceImage = ImageHelper::read($faceImage);
    }
    if (ImageHelper::isBase64($faceImage)) {
      $faceImage = ImageHelper::convertBase64ToBlob($faceImage);
    }
    $numberOfFaces = $this->getNumberOfFaces($faceImage);
    if ($numberOfFaces === 0) {
      return null;
    }
    $response = $this->client->searchFacesByImage([
      'CollectionId' => $collectionId,
      'FaceMatchThreshold' => $threshold,
      'Image' => [ 'Bytes' => $faceImage],
      'MaxFaces' => 1,
    ])->toArray();
    if ($this->debug) {
      Logger::debug('Response: ', $response);
    }
    $status = $response['@metadata']['statusCode'] ?? null;
    if ($status != 200) {
      throw new \RuntimeException('Collection getting error');
    }
    if (empty($response['FaceMatches'])) {
      return null;
    }
    return [
      'faceId' => $response['FaceMatches'][0]['Face']['FaceId'],
      'similarity' => round($response['FaceMatches'][0]['Similarity'], 1)
    ];
  }

  /**
   * @param  string $collectionId
   * @param  string $faceImage
   * @return bool
   */
  public function existsFaceFromCollection(string $collectionId, string $faceImage, int $threshold = 80): bool {
    return !empty($this->getFaceFromCollectionByImage($collectionId, $faceImage, $threshold));
  }

  /**
   * @param  string $collectionId
   * @param  array  $faceIds
   * @return void
   */
  public function deleteFaceFromCollection(string $collectionId, array $faceIds) {
    $response = $this->client->deleteFaces([ 'CollectionId' => $collectionId, 'FaceIds' => $faceIds ])->toArray();
    if ($this->debug) {
      Logger::debug('Response: ', $response);
    }
    $status = $response['@metadata']['statusCode'] ?? null;
    if ($status != 200) {
      throw new \RuntimeException('Collection face deletion error');
    }
  }

  /**
   * @param  string $baseDir
   * @return string
   */
  public function generateCollectionId(string $baseDir): string {

    do {

      $tmp = rtrim($baseDir, '/') . '/' . uniqid(bin2hex(random_bytes(1)));
    } while(file_exists($tmp));
    return basename($tmp);
  }

  /**
   * @param  string $faceImage1
   * @param  string $faceImage2
   * @return float
   */
  public function compareFaces(string $faceImage1, string $faceImage2): float {
    if (\preg_match('/^\//', $faceImage1) && \is_file($faceImage1)) {
      $faceImage1 = ImageHelper::read($faceImage1);
    }
    if (\preg_match('/^\//', $faceImage2) && \is_file($faceImage2)) {
      $faceImage2 = ImageHelper::read($faceImage2);
    }
    $response = $this->client->compareFaces([
      'SimilarityThreshold' => 0,
      'SourceImage' => [ 'Bytes' => ImageHelper::isBase64($faceImage1) ? ImageHelper::convertBase64ToBlob($faceImage1) : $faceImage1 ],
      'TargetImage' => [ 'Bytes' => ImageHelper::isBase64($faceImage2) ? ImageHelper::convertBase64ToBlob($faceImage2) : $faceImage2 ]
    ])->toArray();
    if ($this->debug) {
      Logger::debug('Response: ', $response);
    }
    $status = $response['@metadata']['statusCode'] ?? null;
    if ($status != 200) {
      throw new \RuntimeException('Calculate similarit error');
    }
    if (empty($response['FaceMatches'])) {
      return .0;
    }
    return round($response['FaceMatches'][0]['Similarity'], 1);
  }

  /**
   * @param  string      $faceImage
   * @param  int|integer $threshold
   * @param  string      $attributes DEFAULT|ALL
   * @return array
   */
  public function detectionFaces(string $faceImage, int $threshold = 90, $attributes = 'DEFAULT'): array {
    if (\preg_match('/^\//', $faceImage) && \is_file($faceImage)) {
      $faceImage = ImageHelper::read($faceImage);
    }
    $response = $this->client->DetectFaces([
      'Image' => [ 'Bytes' => ImageHelper::isBase64($faceImage) ? ImageHelper::convertBase64ToBlob($faceImage) : $faceImage ],
      'Attributes' => [ $attributes ]])->toArray();
    if ($this->debug) {
      Logger::debug('Response: ', $response);
    }
    $status = $response['@metadata']['statusCode'] ?? null;
    if ($status != 200) {
      throw new \RuntimeException('Face detection error');
    }
    if (empty($response['FaceDetails'])) {
      return [];
    }
    return array_filter($response['FaceDetails'], function(array $face) use ($threshold) {
      return $face['Confidence'] >= $threshold;
    });
  }

  /**
   * @param  string      $faceImage
   * @param  int|integer $threshold
   * @return int
   */
  public function getNumberOfFaces(string $faceImage, int $threshold = 90): int {
    return count($this->detectionFaces($faceImage, $threshold));
  }
}