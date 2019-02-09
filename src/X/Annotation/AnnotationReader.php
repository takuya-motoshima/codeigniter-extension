<?php
/**
 *
 * Annotation reader class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2018 Takuya Motoshima
 */
namespace X\Annotation;
use \X\Annotation\AccessControl;
use \X\Util\Logger;
final class AnnotationReader
{
  /**
   * 
   * @param  string  $class
   * @param  string  $method
   * @return bool
   */
  public static function accessControl(string $class, string $method): bool
  {
    $annotations = self::reader()->getMethodAnnotations(new \ReflectionMethod($class, $method));
    Logger::d('$annotations=', $annotations);
    return true;
  }

  /**
   * 
   * Get annotation leader
   * 
   * @return \Doctrine\Common\Annotations\AnnotationReader
   */
  private static function reader(): \Doctrine\Common\Annotations\AnnotationReader
  {
    static $reader = null;
    if (isset($reader)) {
      return $reader;
    }
    $annotationPath = __DIR__ . '/AccessControl.php';
    Logger::d('annotationPath=', $annotationPath);
    \Doctrine\Common\Annotations\AnnotationRegistry::registerFile($annotationPath);
    $reader = new \Doctrine\Common\Annotations\AnnotationReader();
    return $reader;
  }
}