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
   * Add collection
   *
   * @param string $id
   * @return void
   */
  public function add(string $id) {
    try {
      $res = $this->rekognition
        ->createCollection(['CollectionId' => $id])
        ->toArray();
      $this->debug && Logger::debug('Collection creation result: ', $res);
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
      $res = $this->rekognition
        ->describeCollection(['CollectionId' => $id])
        ->toArray();
      $this->debug && Logger::debug('Collection search results: ', $res);
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
      $res = $this->rekognition
        ->listCollections()
        ->toArray();
      $this->debug && Logger::debug('All collection search results: ', $res);
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
      $res = $this->rekognition
        ->deleteCollection(['CollectionId' => $id])
        ->toArray();
      $this->debug && Logger::debug('Collection deletion result: ', $res);
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