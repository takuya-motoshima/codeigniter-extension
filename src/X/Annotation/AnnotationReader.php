<?php
namespace X\Annotation;
use \X\Annotation\Access;
use \X\Util\Logger;

/**
 * Read the annotations of the controller's methods.
 */
final class AnnotationReader {
  /**
   * Get Access annotation information.
   * The Access annotation contains the following fields.
   * - allow_login: Set to true to allow access for logged-in users or false to disallow access.
   * - allow_logoff: Set to true to allow access for logoff users, or false to disallow access.
   * - allow_role: Role names of logged-in users to be allowed access. You can specify multiple comma-separated names.
   * - allow_http: Set to true to allow access from HTTP, false to disallow. For example, if you want to allow access only from the CLI, set false.
   * @param string $class Controller class name.
   * @param string $method Method name.
   * @return array{allow_login: bool, allow_logoff: bool, allow_role: string, allow_http: bool} Access annotation object.
   */
  public static function getAccessibility(string $class, string $method): object {
    $annotation = self::getMethodAnnotation($class, $method, 'Access');
    if (empty($annotation))
      return json_decode(json_encode(new Access()));
    return $annotation;
  }

  /**
   * Get method annotation.
   * @param string $class Controller class name.
   * @param string $method Method name.
   * @param string $annotationName Annotation Name.
   * @return object|null Method annotation object.
   */
  private static function getMethodAnnotation(string $class, string $method, string $annotationName): ?object {
    $annotations = self::reader()->getMethodAnnotations(new \ReflectionMethod(ucfirst($class), $method));
    if (empty($annotations))
      return null;
    foreach ($annotations as $annotation) {
      if ((new \ReflectionClass($annotation))->getShortName() === $annotationName)
        return json_decode(json_encode($annotation));
    }
    return null;
  }

  /**
   * Get AnnotationReader instance.
   * @return \Doctrine\Common\Annotations\AnnotationReader AnnotationReader instance.
   */
  private static function reader(): \Doctrine\Common\Annotations\AnnotationReader {
    static $reader = null;
    if (isset($reader))
      return $reader;
    \Doctrine\Common\Annotations\AnnotationRegistry::registerFile(__DIR__ . '/Access.php');
    $reader = new \Doctrine\Common\Annotations\AnnotationReader();
    return $reader;
  }
}