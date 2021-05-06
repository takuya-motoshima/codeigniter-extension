<?php
/**
 *
 * Annotation reader class
 *
 * Step 1: Add access control to the hook(application/config/hooks.php).
 *
 * ```php
 * use \X\Annotation\AnnotationReader;
 * 
 * // Add access control to hooks.
 * $hook['post_controller_constructor'] = function() {
 *   $ci =& get_instance();
 * 
 *   // Get access from annotations.
 *   $accessibility = AnnotationReader::getAccessibility($ci->router->class, $ci->router->method);
 * 
 *   // Whether you are logged in.
 *   $islogin = !empty($_SESSION['user']);
 * 
 *   // Whether it is HTTP access.
 *   $ishttp = !is_cli();
 * 
 *   // Request URL.
 *   $requesturl = $ci->router->directory . $ci->router->class . '/' . $ci->router->method;
 * 
 *   // When accessed by HTTP.
 *   if ($ishttp) {
 *     // Returns an error if HTTP access is not allowed.
 *     if (!$accessibility->allow_http) throw new \RuntimeException('HTTP access is not allowed');
 * 
 *     // When the logged-in user calls a request that only the log-off user can access, redirect to the dashboard.
 *     // It also redirects to the login page when the log-off user calls a request that only the logged-in user can access.
 *     if ($islogin && !$accessibility->allow_login) redirect('/dashboard');
 *     else if (!$islogin && !$accessibility->allow_logoff) redirect('/login');
 *   } else {
 *     // When executed with CLI.
 *   }
 * };
 * ```
 *
 * Step 2: Define annotations for public methods on each controller.
 * 
 * ```php
 * use \X\Annotation\Access;
 * 
 * \/**
 *  * Only log-off users can access it.
 *  * 
 *  * @Access(allow_login=false, allow_logoff=true)
 *  *\/
 * public function login() {}
 * 
 * \/**
 *  * Only logged-in users can access it..
 *  * 
 *  * @Access(allow_login=true, allow_logoff=false)
 *  *\/
 * public function dashboard() {}
 * 
 * \/**
 *  * It can only be done with the CLI.
 *  * 
 *  * @Access(allow_http=false)
 *  *\/
 * public function batch() {}
 * ```
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2018 Takuya Motoshima
 */
namespace X\Annotation;
use \X\Annotation\Access;

final class AnnotationReader {

  /**
   *  Get method accessibility
   * 
   * @param  string  $class
   * @param  string  $method
   * @return object 
   */
  public static function getAccessibility(string $class, string $method): object {
    $annotation = self::getMethodAnnotations($class, $method, 'Access');
    if (empty($annotation)) {
      return json_decode(json_encode(new Access()));
    }
    return $annotation;
  }

  /**
   * 
   * Get method annotations
   * 
   * @param  string  $class
   * @param  string  $method
   * @param  string  $name
   * @return object|array
   */
  private static function getMethodAnnotations(string $class, string $method, string $name = null) {
    $objects = self::reader()->getMethodAnnotations(new \ReflectionMethod(ucfirst($class), $method));
    if (empty($objects)) {
      return null;
    }
    $annotations = [];
    foreach ($objects as $object) {
      $className = (new \ReflectionClass($object))->getShortName();
      if ($className === $name) {
        return json_decode(json_encode($object));
      }
      $annotations[$className] = json_decode(json_encode($object));
    }
    return $annotations;
  }

  /**
   * 
   * Get annotation reader
   * 
   * @return \Doctrine\Common\Annotations\AnnotationReader
   */
  private static function reader(): \Doctrine\Common\Annotations\AnnotationReader {
    static $reader = null;
    if (isset($reader)) {
      return $reader;
    }
    \Doctrine\Common\Annotations\AnnotationRegistry::registerFile(__DIR__ . '/Access.php');
    $reader = new \Doctrine\Common\Annotations\AnnotationReader();
    return $reader;
  }
}