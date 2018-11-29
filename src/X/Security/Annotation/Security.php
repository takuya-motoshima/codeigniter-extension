<?php
/**
 *
 * Security annotation
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2018 Takuya Motoshima
 */
namespace X\Security\Annotation;
use Doctrine\Common\Annotations\Annotation;
class Security
{
  /**
   * allow: accessible if logged in.
   * deny: can not access if logged in
   * @var string
   */
  public $loggedin;

  /**
   * allow: accessible when logging off
   * deny: can not access if logging off
   * @var string
   */
  public $loggedoff;
}