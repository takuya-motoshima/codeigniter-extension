<?php
/**
 *
 * Annotation reader class
 *
 * e.g.:
 * 
 * // application/config/hooks.php:
 * use \X\Annotation\AnnotationReader;
 * use \X\Util\Logger;
 * $hook['post_controller_constructor'] = function() {
 *   $ci =& get_instance();
 *   $accessibility = AnnotationReader::getAccessibility($ci->router->class, $ci->router->method);
 *   Logger::d('$accessibility=', $accessibility);
 *   $loggedin = !empty($_SESSION['user']);
 *   if ($loggedin && !$accessibility->allow_login) {
 *     // When the login user performs a non-access action.
 *     redirect('/dashboard');
 *   } else if (!$loggedin && !$accessibility->allow_logoff) {
 *     // Logoff user performs a non-access action.
 *     redirect('/login');
 *   }
 * };
 * 
 * // application/controllers/Login.php:
 * use \X\Annotation\Access;
 * class Login extends AppController {
 *   \/**
 *    * @Access(allow_login=false, allow_logoff=true)
 *    *\/
 *   public function index() {
 *     parent::view('login');
 *   }
 * }
 *
 * // application/controllers/Dashboard.php:
 * use \X\Annotation\Access;
 * class Dashboard extends AppController {
 *   \/**
 *    * @Access(allow_login=true, allow_logoff=false)
 *    *\/
 *   public function index() {
 *     parent::view('dashboard');
 *   }
 * }
 * 
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