<?php
namespace X\Annotation;
use \Doctrine\Common\Annotations\Annotation;

/**
 *
 * AccessControl annotation
 *
 * @Annotation
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2018 Takuya Motoshima
 */
class AccessControl
{
  public $allowLoggedin = true;
  public $allowLoggedoff = true;
}