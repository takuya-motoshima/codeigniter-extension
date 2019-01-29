<?php
namespace X\Security\Annotation;
use Doctrine\Common\Annotations\Annotation;

/**
 *
 * Security annotation
 *
 * @Annotation
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2018 Takuya Motoshima
 */
class Security
{
  public $loggedin;
  public $loggedoff;
}