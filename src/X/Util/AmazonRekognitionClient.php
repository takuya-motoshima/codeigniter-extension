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
class AmazonRekognitionClient {

  protected $client;

  /**
   * 
   * construct
   *
   * @param array $config
   */
  public function __construct(array $config = []) {
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



  // ----------------------------------------------------------------
  // Operate Face not a collection

  /**
   * 
   * Compare face
   * 
   * @param  string      $srcImgBlob Image binary data
   * @param  string      $dstImgBlob Image binary data
   * @param  int $threshold
   * @return bool
   */
  public function compareFace(string $srcImgBlob, string $dstImgBlob, int $threshold = 80): bool {
    try {
      if (ImageHelper::isBase64($srcImgBlob)) {
        $srcImgBlob = ImageHelper::convertBase64ToBlob($srcImgBlob);
      }
      if (ImageHelper::isBase64($dstImgBlob)) {
        $dstImgBlob = ImageHelper::convertBase64ToBlob($dstImgBlob);
      }
      $res = $this->client->compareFaces([
        'SimilarityThreshold' => $threshold,
        'SourceImage' => [
          'Bytes' => $srcImgBlob
        ],
        'TargetImage' => [
          'Bytes' => $dstImgBlob
        ]
      ]);
      $res = $res->toArray();
      // Logger::d('$res=', $res);
      $status = !empty($res['@metadata']['statusCode']) ? (int) $res['@metadata']['statusCode'] : null;
      // Logger::d('$status=', $status);
      if ($status !== 200) {
        throw new \RuntimeException('Face comparison error');
      }
      return $res['FaceMatches'] ? true : false;
    } catch (RekognitionException $e) {
      Logger::e($e);
      throw $e;
    } catch (Throwable $e) {
      Logger::e($e);
      throw $e;
    }
  }

  /**
   * 
   * Compare face by path
   * 
   * @param  string      $srcImgPath Image path
   * @param  string      $dstImgPath Image path
   * @param  int $threshold
   * @return bool
   */
  public function compareFaceByPath(string $srcImgPath, string $dstImgPath, int $threshold = 80): bool {
    return $this->compareFace(
      ImageHelper::read($srcImgPath),
      ImageHelper::read($dstImgPath),
      $threshold
    );
  }

  /**
   * 
   * Detect face
   *
   * @param string $imgBlob Image binary data
   * @param int $threshold
   * @return array
   */
  public function detectFace(string $imgBlob, int $threshold = 90): array {
    try {
      if (ImageHelper::isBase64($imgBlob)) {
        $imgBlob = ImageHelper::convertBase64ToBlob($imgBlob);
      }
      $res = $this->client->DetectFaces([
        'Image' => ['Bytes' => $imgBlob],
        'Attributes' => ['DEFAULT']
      ]);
      $res = $res->toArray();
      // Logger::d('$res=', $res);
      $status = !empty($res['@metadata']['statusCode']) ? (int) $res['@metadata']['statusCode'] : null;
      if ($status !== 200) {
        throw new \RuntimeException('Face detection error');
      }
      if (empty($res['FaceDetails'])) {
        return [];
      }
      $faces = $res['FaceDetails'];
      return array_filter($res['FaceDetails'], function(array $face) use($threshold) {
        return $face['Confidence'] >= $threshold;
      });
    } catch (Throwable $e) {
      Logger::e($e);
      throw $e;
    }
  }

  /**
   * 
   * Is face by path
   *
   * @param string $imgPath Image path
   * @param int $threshold
   * @return array
   */
  public function detectFaceByPath(string $imgPath, int $threshold = 90): array {
    return $this->detectFace(ImageHelper::read($imgPath), $threshold);
  }


  /**
   * 
   * Count face
   *
   * @param string $imgBlob Image binary data
   * @param int $threshold
   * @return int
   */
  public function countFace(string $imgBlob, int $threshold = 90): int {
    $faces = $this->detectFace($imgBlob, $threshold);
    return count($faces);
  }

  /**
   * 
   * Count face by path
   *
   * @param string $imgPath Image path
   * @param int $threshold
   * @return int
   */
  public function countFaceByPath(string $imgPath, int $threshold = 90): int {
    $faces = $this->detectFaceByPath($imgPath, $threshold);
    // Logger::d('$faces=', $faces);
    return count($faces);
  }


  // ----------------------------------------------------------------
  // Collection operation
  /**
   * 
   * Add collection
   *
   * @param string $collectionId
   * @return void
   */
  public function addCollection(string $collectionId) {
    try {
      $res = $this->client->createCollection(['CollectionId' => $collectionId]);
      $res = $res->toArray();
      // Logger::d('$res=', $res);
      $status = !empty($res['StatusCode']) ? (int) $res['StatusCode'] : null;
      if ($status !== 200) {
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
   * Get collection
   *
   * @param string $collectionId
   * @return void
   */
  public function getCollection(string $collectionId): ?array {
    try {
      $res = $this->client->describeCollection(['CollectionId' => $collectionId]);
      $res = $res->toArray();
      // Logger::d('$res=', $res);
      $status = !empty($res['@metadata']['statusCode']) ? (int) $res['@metadata']['statusCode'] : null;
      if ($status !== 200) {
        throw new \RuntimeException('Collection getting error');
      }
      return [
        'FaceCount' => $res['FaceCount'],
        'FaceModelVersion' => $res['FaceModelVersion'],
        'CollectionARN' => $res['CollectionARN'],
        'CreationTimestamp' => $res['CreationTimestamp'],
      ];
    } catch (RekognitionException $e) {
      if ($e->getAwsErrorCode() !== 'ResourceNotFoundException') {
        throw $e;
      }
      return null;
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
  public function deleteCollection(string $collectionId) {
    try {
      $res = $this->client->deleteCollection(['CollectionId' => $collectionId]);
      $res = $res->toArray();
      // Logger::d('$res=', $res);
      $status = !empty($res['StatusCode']) ? (int) $res['StatusCode'] : null;
      if ($status !== 200) {
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
   * 
   * Exists collection
   *
   * @param string $collectionId
   * @return void
   */
  public function existsCollection(string $collectionId): bool {
    $res = $this->getCollection($collectionId);
    // Logger::d('$res=', $res);
    return !empty($res);
  }



  // ----------------------------------------------------------------
  // Operation of A in the collection
  /**
   * 
   * Add face to coolection
   *
   * @param string $collectionId
   * @param string $imgBlob  Image binary data
   * @return bool
   */
  public function addFaceToCollection(string $collectionId, string $imgBlob): string {
    try {
      if (ImageHelper::isBase64($imgBlob)) {
        $imgBlob = ImageHelper::convertBase64ToBlob($imgBlob);
      }
      $faceCount = $this->countFace($imgBlob);
      if ($faceCount === 0) {
        throw new \RuntimeException('Face not detected');
      } else if ($faceCount > 1) {
        throw new \RuntimeException('Multiple faces can not be registered');
      }
      $res = $this->client->indexFaces([
        'CollectionId' => $collectionId,
        'DetectionAttributes' => ['ALL'],
        // 'ExternalImageId' => '',
        'Image' => ['Bytes' => $imgBlob],
      ]);
      $res = $res->toArray();
      // Logger::d('$res=', $res);
      $status = !empty($res['@metadata']['statusCode']) ? (int) $res['@metadata']['statusCode'] : null;
      if ($status !== 200) {
        throw new \RuntimeException('Collection face registration error');
      }
      if (empty($res['FaceRecords'])) {
        throw new \RuntimeException('This image does not include faces');
      }
      return $res['FaceRecords'][0]['Face']['FaceId'];
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
  public function getFacesFromCollection(string $collectionId): array {
    try {
      $res = $this->client->listFaces(['CollectionId' => $collectionId, 'MaxResults' => 4096,]);
      $res = $res->toArray();
      // Logger::d('$res=', $res);
      $status = !empty($res['@metadata']['statusCode']) ? (int) $res['@metadata']['statusCode'] : null;
      if ($status !== 200) {
        throw new \RuntimeException('Collection face list acquisition error');
      }
      return $res['Faces'];
    } catch (Throwable $e) {
      Logger::e($e);
      throw $e;
    }
  }

  /**
   * 
   * Match face from collection
   *
   * @param  string $collectionId
   * @param  string $imgBlob
   * @return bool
   */
  public function matchFaceFromCollection(string $collectionId, string $imgBlob, ?string &$faceId = null, int $threshold = 95): bool {
    try {
      if (ImageHelper::isBase64($imgBlob)) {
        $imgBlob = ImageHelper::convertBase64ToBlob($imgBlob);
      }
      $faceCount = $this->countFace($imgBlob);
      if ($faceCount === 0) {
        throw new \RuntimeException('Face not detected');
      } else if ($faceCount > 1) {
        throw new \RuntimeException('Multiple faces can not be matched');
      }
      $res = $this->client->searchFacesByImage([
        'CollectionId' => $collectionId,
        'FaceMatchThreshold' => $threshold,
        'Image' => ['Bytes' => $imgBlob],
        'MaxFaces' => 1,
      ]);
      $res = $res->toArray();
      // Logger::d('$res=', $res);
      $status = !empty($res['@metadata']['statusCode']) ? (int) $res['@metadata']['statusCode'] : null;
      if ($status !== 200) {
        throw new \RuntimeException('Collection getting error');
      }
      if (empty($res['FaceMatches'])) {
        return false;
      }
      $faceId = $res['FaceMatches'][0]['Face']['FaceId'];
      return true;
    } catch (Throwable $e) {
      Logger::e($e);
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
  public function deleteFaceFromCollection(string $collectionId, array $faceIds): array {
    try {
      Logger::d('$collectionId=', $collectionId);
      Logger::d('$faceIds=', $faceIds);
      $res = $this->client->deleteFaces(['CollectionId' => $collectionId, 'FaceIds' => $faceIds]);
      $res = $res->toArray();
      // Logger::d('$res=', $res);
      $status = !empty($res['@metadata']['statusCode']) ? (int) $res['@metadata']['statusCode'] : null;
      if ($status !== 200) {
        throw new \RuntimeException('Collection face deletion error');
      }
      $deletedFaces = $res['DeletedFaces'] ?? [];
      Logger::d('$deletedFaces=', $deletedFaces);
      return $deletedFaces;
    } catch (RekognitionException $e) {
      Logger::e($e);
      throw $e;
    } catch (Throwable $e) {
      Logger::e($e);
      throw $e;
    }
  }
}