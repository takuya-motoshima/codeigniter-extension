<?php
namespace X\Rekognition;
use \Aws\Rekognition\RekognitionClient;
use \Aws\Rekognition\Exception\RekognitionException;
use \X\Util\ImageHelper;
use \X\Util\Logger;

/**
 * Amazon Rekognition API Client.
 */
class Client {
  /**
   * RekognitionClient instance.
   * @var RekognitionClient
   */
  private $client;

  /**
   * Debug mode.
   * @var bool
   */
  private $debug;
 
  /**
   * Initialize Amazon Rekognition API client.
   * @param string $options[region] AWS Region to connect to.The default is "ap-northeast-1".
   * @param string $options[key] AWS access key ID.This is required.
   * @param string $options[secret] AWS secret access key.This is required.
   * @param int $options[connect_timeout] A float describing the number of seconds to wait while trying to connect to a server. The default is 5 (seconds).
   * @param bool $options[debug] Specify true to output the result of Rekognition to the debug log.The default is false and no debug log is output.
   */
  public function __construct(array $options) {
    $options = array_merge([
      'region' => 'ap-northeast-1',
      'key' => null,
      'secret' => null,
      'connect_timeout' => 5,
      'debug' => false
    ], $options);
    if (empty($options['key']))
      throw new \RuntimeException('Amazon Rekognition access key is required');
    else if (empty($options['secret']))
      throw new \RuntimeException('Amazon Rekognition secret key is required');
    if ($options['debug'])
      Logger::debug('Options: ', $options);
    $this->client = new RekognitionClient([
      'region' => $options['region'],
      'version' => 'latest',
      'credentials' => [
        'key' => $options['key'],
        'secret' => $options['secret']
      ],
      'http' => [
        'connect_timeout' => $options['connect_timeout']
      ]
    ]);
    $this->debug = $options['debug'];
  }

  /**
   * Add a collection.
   * @param string $collectionId Collection ID.
   * @return void
   */
  public function addCollection(string $collectionId): void {
    try {
      $res = $this->client->createCollection(['CollectionId' => $collectionId])->toArray();
      if ($this->debug)
        Logger::debug('Response: ', $res);
      $status = $res['StatusCode'] ?? null;
      if ($status != 200)
        throw new \RuntimeException('Collection could not be created');
    } catch (RekognitionException $e) {
      if ($e->getAwsErrorCode() !== 'ResourceAlreadyExistsException')
        throw $e;
    }
  }

  /**
   * Get Collection.
   * @param string $collectionId Collection ID.
   * @return mixed Collection Data.
   */
  public function getCollection(string $collectionId): ?array {
    try {
      $res = $this->client->describeCollection(['CollectionId' => $collectionId])->toArray();
      if ($this->debug)
        Logger::debug('Response: ', $res);
      $status = $res['@metadata']['statusCode'] ?? null;
      if ($status != 200)
        throw new \RuntimeException('Collection getting error');
      return $res;
    } catch (RekognitionException $e) {
      if ($e->getAwsErrorCode() !== 'ResourceNotFoundException')
        throw $e;
      return null;
    }
  }

  /**
   * Get all collections.
   * @return string[] List of collection data.
   */
  public function getAllCollections(): array {
    $res = $this->client->listCollections()->toArray();
    if ($this->debug)
      Logger::debug('Response: ', $res);
    $status = $res['@metadata']['statusCode'] ?? null;
    if ($status != 200)
      throw new \RuntimeException('Collection getting error');
    return !empty($res['CollectionIds']) ? $res['CollectionIds'] : [];
  }

  /**
   * Delete a collection.
   * @param string $collectionId Collection ID.
   * @return void
   */
  public function deleteCollection(string $collectionId): void {
    try {
      $res = $this->client->deleteCollection(['CollectionId' => $collectionId])->toArray();
      if ($this->debug)
        Logger::debug('Response: ', $res);
      $status = $res['StatusCode'] ?? null;
      if ($status != 200)
        throw new \RuntimeException('Collection could not be delete');
    } catch (RekognitionException $e) {
      if ($e->getAwsErrorCode() !== 'ResourceNotFoundException')
        throw $e;
    }
  }

  /**
   * Check if a collection exists.
   * @param string $collectionId Collection ID.
   * @return bool Whether the collection exists or not.
   */
  public function existsCollection(string $collectionId): bool {
    return !empty($this->getCollection($collectionId));
  }

  /**
   * Add a face to the collection.
   * @param string $collectionId Collection ID.
   * @param string $faceImage Data URL, Blob, or path of face images.
   * @return string Face ID.
   */
  public function addFaceToCollection(string $collectionId, string $faceImage): string {
    if (ImageHelper::isDataURL($faceImage))
      // If the image is a data URL, convert it to a blob.
      $faceImage = ImageHelper::dataURL2Blob($faceImage);
    $faceCount = $this->getNumberOfFaces($faceImage);
    if ($faceCount === 0)
      throw new \RuntimeException('Face not detected');
    else if ($faceCount > 1)
      throw new \RuntimeException('Multiple faces can not be registered');
    $res = $this->client->indexFaces([
      'CollectionId' => $collectionId,
      'DetectionAttributes' => [ 'ALL' ],
      'Image' => [
        'Bytes' => $faceImage
      ]
    ])->toArray();
    if ($this->debug)
      Logger::debug('Response: ', $res);
    $status = $res['@metadata']['statusCode'] ?? null;
    if ($status != 200)
      throw new \RuntimeException('Collection face registration error');
    if (empty($res['FaceRecords']))
      throw new \RuntimeException('This image does not include faces');
    return $res['FaceRecords'][0]['Face']['FaceId'];
  }

  /**
   * Get face data from a collection.
   * @param string $collectionId Collection ID.
   * @param int $maxResults (optional) Maximum number of face data to retrieve. Default is 4096.
   * @return array List of face data.
   */
  public function getAllFacesFromCollection(string $collectionId, int $maxResults=4096): array {
    $res = $this->client->listFaces(['CollectionId' => $collectionId, 'MaxResults' => $maxResults ])->toArray();
    if ($this->debug)
      Logger::debug('Response: ', $res);
    $status = $res['@metadata']['statusCode'] ?? null;
    if ($status != 200)
      throw new \RuntimeException('Collection face list acquisition error');
    return $res['Faces'];
  }

  /**
   * Get the face data most similar to the face image from the collection.
   * @param string $collectionId Collection ID.
   * @param string $faceImage Data URL, Blob, or path of face images.
   * @param int $threshold (optional) Face match threshold (in percent). Default is 80.
   * @return array Face data.
   */
  public function getFaceFromCollectionByImage(string $collectionId, string $faceImage, int $threshold=80): ?array {
    $maxFaces = 1;
    $detections = $this->getMultipleFacesFromCollectionByImage($collectionId, $faceImage, $threshold, $maxFaces);
    return !empty($detections) ? $detections[0]: null;
  }

  /**
   * Get all face data from the collection that are similar to the face image.
   * @param string $collectionId Collection ID.
   * @param string $faceImage Data URL, Blob, or path of face images.
   * @param int $threshold (optional) Face match threshold (in percent). Default is 80.
   * @param int $maxResults (optional) Maximum number of face data to retrieve. Default is 4096.
   * @return array Face data.
   */
  public function getMultipleFacesFromCollectionByImage(string $collectionId, string $faceImage, int $threshold=80, int $maxFaces=4096): array {
    if (\preg_match('/^\//', $faceImage) && \is_file($faceImage))
      // If the image is a file path, read it as DataURL.
      $faceImage = ImageHelper::readAsBlob($faceImage);
    if (ImageHelper::isDataURL($faceImage))
      // If the image is a data URL, convert it to a blob.
      $faceImage = ImageHelper::dataURL2Blob($faceImage);
    $faceCount = $this->getNumberOfFaces($faceImage);
    if ($faceCount === 0)
      return [];
    $res = $this->client->searchFacesByImage([
      'CollectionId' => $collectionId,
      'FaceMatchThreshold' => $threshold,
      'Image' => [ 'Bytes' => $faceImage],
      'MaxFaces' => $maxFaces
    ])->toArray();
    if ($this->debug)
      Logger::debug('Response: ', $res);
    $status = $res['@metadata']['statusCode'] ?? null;
    if ($status != 200)
      throw new \RuntimeException('Collection getting error');
    if (empty($res['FaceMatches']))
      return [];
    $detections = array_map(function(array $faceMatche) {
      return [
        'faceId' => $faceMatche['Face']['FaceId'],
        'similarity' => round($faceMatche['Similarity'], 1)
      ];
    }, $res['FaceMatches']);
    return $detections;
  }

  /**
   * Check if a face exists in the collection.
   * @param string $collectionId Collection ID.
   * @param string $faceImage Data URL, Blob, or path of face images.
   * @param int $threshold (optional) Face match threshold (in percent). Default is 80.
   * @return bool Whether the face exists in the collection or not.
   */
  public function existsFaceFromCollection(string $collectionId, string $faceImage, int $threshold=80): bool {
    return !empty($this->getFaceFromCollectionByImage($collectionId, $faceImage, $threshold));
  }

  /**
   * Delete a face from the collection.
   * @param string $collectionId Collection ID.
   * @param string[] $faceIds Face ID list.
   * @return void
   */
  public function deleteFaceFromCollection(string $collectionId, array $faceIds): void {
    $res = $this->client->deleteFaces([ 'CollectionId' => $collectionId, 'FaceIds' => $faceIds ])->toArray();
    if ($this->debug)
      Logger::debug('Response: ', $res);
    $status = $res['@metadata']['statusCode'] ?? null;
    if ($status != 200)
      throw new \RuntimeException('Collection face deletion error');
  }

  /**
   * Generate collection ID.
   * @return string Collection ID.
   */
  public function generateCollectionId(): string {
    return uniqid(bin2hex(random_bytes(1)));
  }

  /**
   * Comparison of faces.
   * @param string $faceImage1 Data URL, Blob, or path of face images.
   * @param string $faceImage2 Data URL, Blob, or path of face images.
   * @return float Similarity rate (rate) of two faces.
   */
  public function compareFaces(string $faceImage1, string $faceImage2): float {
    if (\preg_match('/^\//', $faceImage1) && \is_file($faceImage1))
      // If the image is a file path, read it as DataURL.
      $faceImage1 = ImageHelper::readAsBlob($faceImage1);
    if (\preg_match('/^\//', $faceImage2) && \is_file($faceImage2))
      // If the image is a file path, read it as DataURL.
      $faceImage2 = ImageHelper::readAsBlob($faceImage2);
    if ($this->getNumberOfFaces($faceImage1) === 0 || $this->getNumberOfFaces($faceImage2) === 0)
      // If no face is found in the image, the similarity rate returns zero.
      return .0;

    // Compare the faces in the two images.
    $res = $this->client->compareFaces([
      'SimilarityThreshold' => 0,
      'SourceImage' => [ 'Bytes' => ImageHelper::isDataURL($faceImage1) ? ImageHelper::dataURL2Blob($faceImage1) : $faceImage1 ],
      'TargetImage' => [ 'Bytes' => ImageHelper::isDataURL($faceImage2) ? ImageHelper::dataURL2Blob($faceImage2) : $faceImage2 ]
    ])->toArray();
    if ($this->debug)
      Logger::debug('Response: ', $res);
    $status = $res['@metadata']['statusCode'] ?? null;
    if ($status != 200)
      throw new \RuntimeException('Calculate similarit error');
    if (empty($res['FaceMatches']))
      return .0;
    return round($res['FaceMatches'][0]['Similarity'], 1);
  }

  /**
   * Face detection.
   * @param string $faceImage Data URL, Blob, or path of face images.
   * @param int $threshold (optional) Face recognition threshold (percent). Default is 90.
   * @param 'DEFAULT'|'ALL' (optional) $attributes If 'ALL', more detailed facial information is retrieved. Default is "DEFAULT".
   * @return array Detected face data.
   */
  public function detectionFaces(string $faceImage, int $threshold=90, $attributes='DEFAULT'): array {
    // If the image is a file path, read it as DataURL.
    if (\preg_match('/^\//', $faceImage) && \is_file($faceImage))
      $faceImage = ImageHelper::readAsBlob($faceImage);
    $res = $this->client->DetectFaces([
      'Image' => [ 'Bytes' => ImageHelper::isDataURL($faceImage) ? ImageHelper::dataURL2Blob($faceImage) : $faceImage ],
      'Attributes' => [ $attributes ]])->toArray();
    if ($this->debug)
      Logger::debug('Response: ', $res);
    $status = $res['@metadata']['statusCode'] ?? null;
    if ($status != 200)
      throw new \RuntimeException('Face detection error');
    if (empty($res['FaceDetails']))
      return [];
    return array_filter($res['FaceDetails'], function(array $face) use ($threshold) {
      return $face['Confidence'] >= $threshold;
    });
  }

  /**
   * Get the number of faces in the image.
   * @param string $faceImage Data URL, Blob, or path of face images.
   * @param int $threshold (optional) Face recognition threshold (percent). Default is 90.
   * @return int Number of faces found.
   */
  public function getNumberOfFaces(string $faceImage, int $threshold=90): int {
    return count($this->detectionFaces($faceImage, $threshold));
  }
}