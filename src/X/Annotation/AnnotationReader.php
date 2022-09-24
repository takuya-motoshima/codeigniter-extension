<?php
/**
 * 1. Add access control to the hook(application/config/hooks.php).
 * ```php
 * use \X\Annotation\AnnotationReader;
 * 
 * $hook['post_controller_constructor'] = function() {
 *   $ci =& get_instance();
 *   $accessibility = AnnotationReader::getAccessibility($ci->router->class, $ci->router->method);
 *   $isLogin = !empty($_SESSION['user']);
 *   if (!is_cli()) {
 *     if (!$accessibility->allow_http)
 *       throw new \RuntimeException('HTTP access is not allowed');
 *     if ($isLogin && !$accessibility->allow_login)
 *       redirect('/users/index');
 *     else if (!$isLogin && !$accessibility->allow_logoff)
 *       redirect('/users/login');
 *   }
 * };
 * ```
 *
 * 2. Define annotations for public methods on each controller.
 * ```php
 * use \X\Annotation\Access;
 * 
 * \/**
 *  * Only log-off users can access it.
 *  * @Access(allow_login=false, allow_logoff=true)
 *  *\/
 * public function login() {}
 * 
 * \/**
 *  * Only logged-in users can access it..
 *  * @Access(allow_login=true, allow_logoff=false)
 *  *\/
 * public function dashboard() {}
 * 
 * \/**
 *  * It can only be done with the CLI.
 *  * @Access(allow_http=false)
 *  *\/
 * public function batch() {}
 * ```
 */
namespace X\Annotation;
use \X\Annotation\Access;

final class AnnotationReader {
  /**
   *  Get method accessibility
   */
  public static function getAccessibility(string $class, string $method): object {
    $annotation = self::getMethodAnnotations($class, $method, 'Access');
    if (empty($annotation))
      return json_decode(json_encode(new Access()));
    return $annotation;
  }

  /**
   * Get method annotations
   */
  private static function getMethodAnnotations(string $class, string $method, string $name = null) {
    $objects = self::reader()->getMethodAnnotations(new \ReflectionMethod(ucfirst($class), $method));
    if (empty($objects))
      return null;
    $annotations = [];
    foreach ($objects as $object) {
      $className = (new \ReflectionClass($object))->getShortName();
      if ($className === $name)
        return json_decode(json_encode($object));
      $annotations[$className] = json_decode(json_encode($object));
    }
    return $annotations;
  }

  /**
   * Get annotation reader
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