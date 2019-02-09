<?php
/**
 *
 * Authentication Using Annotations
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2018 Takuya Motoshima
 */
namespace X\Security;
use \Doctrine\Common\Annotations\AnnotationReader;
use \Doctrine\Common\Annotations\AnnotationRegistry;
use \X\Security\Annotation\Security;
use \X\Util\Logger;
final class AnnotationAuthentication
{

  /**
   * 
   * When logging in, you can access if the loggedin property of Security annotation is allow.
   * When logging off, you can access if the loggedin property of Security annotation is allow.
   * 
   * @param  string  $class
   * @param  string  $method
   * @param bool $loggedin
   * @return bool
   */
  public static function isAccessible(string $class, string $method, bool $loggedin): bool
  {
    $method = new \ReflectionMethod($class, $method);
    Logger::d('$method=', $method);
    $annotations = self::reader()->getMethodAnnotations($method);
    Logger::d('$annotations=', $annotations);

    // $annotation = self::reader()->getMethodAnnotation($method, 'Security');
    // Logger::d('$annotation=', $annotation);

    return true;
  }

  /**
   * 
   * Get annotation leader
   * 
   * @return AnnotationReader
   */
  private static function reader(): AnnotationReader
  {
    static $reader = null;
    if (isset($reader)) {
      return $reader;
    }
    Logger::d('annotationPath=', __DIR__ . '/Annotation/Security.php');
    AnnotationRegistry::registerFile(__DIR__ . '/Annotation/Security.php');
    $reader = new AnnotationReader();
    return $reader;
  }
}