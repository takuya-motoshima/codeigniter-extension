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
use \X\Util\Logger;

class CollectionClient {

  protected $client;

  /**
   * 
   * construct
   *
   * @param array $config
   */
  public function __construct(array $config = []) {
    $this->client = new RekognitionClient(array_replace_recursive([
      'region'      => 'ap-northeast-1',
      'version'     => 'latest',
      'credentials' => ['key' => null, 'secret' => null]
    ], $config));
  }

  /**
   * 
   * Add collection
   *
   * @param string $id
   * @return void
   */
  public function add(string $id) {
    try {
      $res = $this->client->createCollection(['CollectionId' => $id]);
      $res = $res->toArray();
      $status = !empty($res['StatusCode']) ? (int) $res['StatusCode'] : null;
      if ($status !== 200) throw new \RuntimeException('Collection could not be created');
    } catch (RekognitionException $e) {
      if ($e->getAwsErrorCode() !== 'ResourceAlreadyExistsException') throw $e;
    } catch (Throwable $e) {
      Logger::error($e);
      throw $e;
    }
  }

  /**
   * 
   * Get collection
   *
   * @param string $id
   * @return void
   */
  public function get(string $id): ?array {
    try {
      $res = $this->client->describeCollection(['CollectionId' => $id]);
      $res = $res->toArray();
      $status = !empty($res['@metadata']['statusCode']) ? (int) $res['@metadata']['statusCode'] : null;
      if ($status !== 200) throw new \RuntimeException('Collection getting error');
      return [
        'FaceCount' => $res['FaceCount'],
        'FaceModelVersion' => $res['FaceModelVersion'],
        'CollectionARN' => $res['CollectionARN'],
        'CreationTimestamp' => $res['CreationTimestamp'],
      ];
    } catch (RekognitionException $e) {
      if ($e->getAwsErrorCode() !== 'ResourceNotFoundException') throw $e;
      return null;
    } catch (Throwable $e) {
      Logger::error($e);
      throw $e;
    }
  }
  /**
   * 
   * Get collection
   *
   * @return array
   */
  public function getAll(): array {
    try {
      $res = $this->client->listCollections();
      $res = $res->toArray();
      $status = !empty($res['@metadata']['statusCode']) ? (int) $res['@metadata']['statusCode'] : null;
      if ($status !== 200) throw new \RuntimeException('Collection getting error');
      if (empty($res['CollectionIds'])) return [];
      return $res['CollectionIds'];
    } catch (Throwable $e) {
      Logger::error($e);
      throw $e;
    }
  }

  /**
   * 
   * Delete collection
   *
   * @param string $id
   * @return void
   */
  public function delete(string $id) {
    try {
      $res = $this->client->deleteCollection(['CollectionId' => $id]);
      $res = $res->toArray();
      $status = !empty($res['StatusCode']) ? (int) $res['StatusCode'] : null;
      if ($status !== 200) throw new \RuntimeException('Collection could not be delete');
    } catch (RekognitionException $e) {
      if ($e->getAwsErrorCode() !== 'ResourceNotFoundException') throw $e;
    } catch (Throwable $e) {
      throw $e;
    }
  }

  /**
   * 
   * Exists collection
   *
   * @param string $id
   * @return void
   */
  public function exists(string $id): bool {
    return !empty($this->get($id));
  }
}