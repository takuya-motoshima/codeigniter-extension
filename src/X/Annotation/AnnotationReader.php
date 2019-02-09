<?php
/**
 *
 * Annotation reader class
 *
 * e.g.:
 *   application/config/hooks.php:
 *      use \X\Annotation\AnnotationReader;
 *      $hook['post_controller_constructor'] = function() {
 *        $ci =& get_instance();
 *        $accessControl = AnnotationReader::getMethodAccessControl($ci->router->class, $ci->router->method);
 *        $loggedin = !empty($_SESSION['user']);
 *        if ($loggedin && !$accessControl->allowLoggedin) {
 *          // In case of an action that the logged-in user can not access
 *          redirect('/dashboard');
 *        } else if (!$loggedin && !$accessControl->allowLoggedoff) {
 *          // In case of an action that can not be accessed by the user who is logging off
 *          redirect('/login');
 *        }
 *      };
 *      
 *   application/ccontrollers/Example.php:
 *      use \X\Annotation\AccessControl;
 *      class Example extends AppController
 *      {
 *        \/**
 *         * @AccessControl(allowLoggedin=false, allowLoggedoff=true)
 *         *\/
 *        public function login() {}
 *      
 *        \/**
 *         * @AccessControl(allowLoggedin=true, allowLoggedoff=false)
 *         *\/
 *        public function dashboard() {}
 *      }
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2018 Takuya Motoshima
 */
namespace X\Annotation;
use \X\Annotation\AccessControl;
final class AnnotationReader
{

  /**
   *  Get method access control
   * 
   * @param  string  $class
   * @param  string  $method
   * @return object
   */
  public static function getMethodAccessControl(string $class, string $method): object
  {
    $annotation = self::getMethodAnnotations($class, $method, 'AccessControl');
    if (empty($annotation)) {
      return json_decode(json_encode(new AccessControl()));
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
  private static function getMethodAnnotations(string $class, string $method, string $name = null)
  {
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
  private static function reader(): \Doctrine\Common\Annotations\AnnotationReader
  {
    static $reader = null;
    if (isset($reader)) {
      return $reader;
    }
    \Doctrine\Common\Annotations\AnnotationRegistry::registerFile(__DIR__ . '/AccessControl.php');
    $reader = new \Doctrine\Common\Annotations\AnnotationReader();
    return $reader;
  }
}