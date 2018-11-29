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
// use Doctrine\Common\Annotations\AnnotationReader;
// use Doctrine\Common\Annotations\AnnotationRegistry;
final class AnnotationAuthentication
{

  public static function isAllowMethod(string $class, string $method): bool
  {
    $method = new \ReflectionMethod($class, $method);
    return preg_match('/@allowLoggedIn/', $method->getDocComment()) === 1;
    // $reader = new AnnotationReader();
    // $annotations = $reader->getMethodAnnotations($method);
  }
}