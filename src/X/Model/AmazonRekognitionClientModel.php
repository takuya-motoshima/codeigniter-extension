<?php
/**
 * Amazon rekognition model
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 * @property CI_DB_query_builder $db
 */
namespace X\Model;
use \X\Util\Loader;
use \X\Util\FileHelper;
use \X\Util\Logger;
use \Aws\Rekognition\RekognitionClient;
use \Aws\Rekognition\Exception\RekognitionException;
class AmazonRekognitionClientModel extends Model
{
    protected $client;

    /**
     * 
     * construct
     *
     * @param array $option
     */
    public function __construct(array $option = [])
    {
        $option = array_replace_recursive([
            'region'      => 'ap-northeast-1',
            'version'     => 'latest',
            'credentials' => [
                'key'    => null,
                'secret' => null
            ]
        ], $option);
        $this->client = new RekognitionClient($option);
        parent::__construct();
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
    public function compare_face(
        string $sourceImageBinary, 
        string $targetImageBinary, 
        int $similarityThreshold = 80
    ): bool
    {
        $response = $this->client->compareFaces([
            'SimilarityThreshold' => $similarityThreshold,
            'SourceImage' => ['Bytes' => $sourceImageBinary],
            'TargetImage' => ['Bytes' => $targetImageBinary]
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
    public function compare_face_by_path(
        string $sourceImagePath, 
        string $targetImagePath, 
        int $similarityThreshold = 80
    ): bool
    {
        return $this->compare_face(
            FileHelper::read_image($sourceImagePath),
            FileHelper::read_image($targetImagePath),
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
    public function is_face(string $imageBinary): bool
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
    public function is_face_by_path(string $imagePath): bool
    {
        $this->is_face(FileHelper::read_image($imagePath));
    }


    /**
     * 
     * Add collection
     *
     * @param string $collectionId
     * @return void
     */
    public function add_collection(string $collectionId)
    {
        try {
            $response = $this->client->createCollection([
                'CollectionId' => $collectionId
            ]);
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
     * Add image
     *
     * @param string $collectionId
     * @param string $imageBinary  Image binary data
     * @return bool
     */
    public function add_image_to_collection(
        string $collectionId, 
        string $imageBinary
    ): array
    {
        try {
            $response = $this->client->indexFaces([
                'CollectionId' => $collectionId,
                'DetectionAttributes' => ['ALL'],
                // 'ExternalImageId' => '',
                'Image' => ['Bytes' => $imageBinary],
            ]);
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
    public function get_faces_from_collection(string $collectionId): array
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
    public function delete_collection(string $collectionId)
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
    public function delete_face_from_collection(string $collectionId, string $imageId)
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
    public function match_faces_from_collection(
        string $collectionId,
        string $imageBinary,
        int $faceMatchThreshold = 70
    ): array
    {
        try {
            $response = $this->client->searchFacesByImage([
                'CollectionId' => $collectionId,
                'FaceMatchThreshold' => $faceMatchThreshold,
                'Image' => ['Bytes' => $imageBinary,],
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