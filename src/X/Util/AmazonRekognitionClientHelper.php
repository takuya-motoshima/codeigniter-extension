<?php
/**
 * Amazon rekognition client
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Util;
use \X\Util\ImageHelper;
use \X\Util\Logger;
use \Aws\Rekognition\RekognitionClient;
use \Aws\Rekognition\Exception\RekognitionException;
class AmazonRekognitionClientHelper
{
  protected $client;

  /**
   * 
   * construct
   *
   * @param array $config
   */
  public function __construct(array $config = [])
  {
    $config = array_replace_recursive([
      'region'      => 'ap-northeast-1',
      'version'     => 'latest',
      'credentials' => [
        'key'    => null,
        'secret' => null
      ]
    ], $config);
    $this->client = new RekognitionClient($config);
  }

  /**
   * 
   * Compare face
   * 
   * @param  string      $sourceImageBinary Image binary data
   * @param  string      $target_image Image binary data
   * @param  int $similarityThreshold
   * @return bool
   */
  public function compareFace(string $sourceImageBinary, string $targetImageBinary, int $similarityThreshold = 80): bool
  {
    $response = $this->client->compareFaces([
      'SimilarityThreshold' => $similarityThreshold,
      'SourceImage' => [
        'Bytes' => $sourceImageBinary
      ],
      'TargetImage' => [
        'Bytes' => $targetImageBinary
      ]
    ]);
    return $response['FaceMatches'] ? true : false;
  }

  /**
   * 
   * Compare face by path
   * 
   * @param  string      $sourceImagePath Image path
   * @param  string      $targetImagePath Image path
   * @param  int $similarityThreshold
   * @return bool
   */
  public function compareFaceByPath(string $sourceImagePath, string $targetImagePath, int $similarityThreshold = 80): bool
  {
    return $this->compareFace(
      ImageHelper::read($sourceImagePath),
      ImageHelper::read($targetImagePath),
      $similarityThreshold
    );
  }

  /**
   * 
   * Is face
   *
   * @param string $imageBinary Image binary data
   * @return bool
   */
  public function isFace(string $imageBinary): bool
  {
    try {
      $response = $this->client->DetectFaces([
        'Image' => [
          'Bytes' => $imageBinary,
        ],
        'Attributes' => ['DEFAULT']
      ]);
      return !empty($response['FaceDetails']);
    } catch (Throwable $e) {
      return false;
    }
  }

  /**
   * 
   * Is face by path
   *
   * @param string $imagePath Image path
   * @return bool
   */
  public function isFaceByPath(string $imagePath): bool
  {
    $this->isFace(ImageHelper::read($imagePath));
  }


  /**
   * 
   * Add collection
   *
   * @param string $collectionId
   * @return void
   */
  public function addCollection(string $collectionId)
  {
    try {
      $response = $this->client->createCollection([
        'CollectionId' => $collectionId
      ]);
      Logger::i('$response=', $response);
      $statusCode = !empty($response['StatusCode']) ? (int) $response['StatusCode'] : null;
      if ($statusCode !== 200) {
        throw new \RuntimeException('Collection could not be created');
      }
    } catch (RekognitionException $e) {
      if ($e->getAwsErrorCode() !== 'ResourceAlreadyExistsException') {
        throw $e;
      }
    } catch (Throwable $e) {
      Logger::e($e);
      throw $e;
    }
  }


  /**
   * 
   * Add face to coolection
   *
   * @param string $collectionId
   * @param string $imageBinary  Image binary data
   * @return bool
   */
  public function addFaceToCollection(string $collectionId, string $imageBinary): array
  {
    try {
      $response = $this->client->indexFaces([
        'CollectionId' => $collectionId,
        'DetectionAttributes' => ['ALL'],
        // 'ExternalImageId' => '',
        'Image' => ['Bytes' => $imageBinary],
      ]);
      Logger::i('$response=', $response);
      $faces = $response['FaceRecords'];
      if (empty($faces)) {
        throw new \RuntimeException('This image does not include faces');
      }
      return [
        'image_id' => $faces[0]['Face']['ImageId'],
        'faces' => array_map(function(array $face): array {
          return [
            'face_id' => $face['Face']['FaceId'],
            'detail' => $face['FaceDetail'],
          ];
        }, $faces)
      ];
    } catch (Throwable $e) {
      Logger::e($e);
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
  public function getFacesFromCollection(string $collectionId): array
  {
    try {
      $response = $this->client->listFaces([
        'CollectionId' => $collectionId,
        'MaxResults' => 4096,
      ]);
      return $response['Faces'];
    } catch (Throwable $e) {
      Logger::e($e);
      throw $e;
    }
  }

  /**
   * 
   * Delete collection
   *
   * @param string $collectionId
   * @return void
   */
  public function deleteCollection(string $collectionId)
  {
    try {
      $response = $this->client->deleteCollection([
        'CollectionId' => $collectionId
      ]);
      $statusCode = !empty($response['StatusCode']) ? (int) $response['StatusCode'] : null;
      if ($statusCode !== 200) {
        throw new \RuntimeException('Collection could not be delete');
      }
    } catch (RekognitionException $e) {
      if ($e->getAwsErrorCode() !== 'ResourceNotFoundException') {
        throw $e;
      }
    } catch (Throwable $e) {
      throw $e;
    }
  }

  /**
   * Delete image from collection
   *
   * @param string $collectionId
   * @param  string $imageId
   * @return void
   */
  public function deleteFaceFromCollection(string $collectionId, string $imageId)
  {
    try {
      $images = $this->get_collection_image($collectionId);
      if (empty($images)) {
        return;
      }
      $deleteIds = [];
      foreach($images as $image) {
        if ($image['ImageId'] === $imageId) {
          $deleteIds[] = $image['FaceId'];
        }
      }
      if (empty($deleteIds)) {
        return;
      }
      $response = $this->client->deleteFaces([
        'CollectionId' => $collectionId,
        'FaceIds' => $deleteIds
      ]);
      return $response;
    } catch (Throwable $e) {
      Logger::e($e);
      throw $e;
    }
  }

  /**
   * 
   * Match faces from collection
   *
   * @param  string $collectionId
   * @param  string $imageBinary
   * @return array Matched image ID
   */
  public function matchFacesFromCollection(string $collectionId, string $imageBinary, int $faceMatchThreshold = 70): array
  {
    try {
      $response = $this->client->searchFacesByImage([
        'CollectionId' => $collectionId,
        'FaceMatchThreshold' => $faceMatchThreshold,
        'Image' => [
          'Bytes' => $imageBinary,
        ],
        'MaxFaces' => 100,
      ]);
      if (empty($response['FaceMatches'])) {
        return [];
      }
      $matchedIds = array_map(function(array $face): string {
        return $face['Face']['ImageId'];
      }, $response['FaceMatches']);
      return $matchedIds;
    } catch (Throwable $e) {
      Logger::e($e);
      throw $e;
    }
  }
}