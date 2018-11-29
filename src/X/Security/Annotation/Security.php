<?php
namespace X\Security\Annotation;
use Doctrine\Common\Annotations\Annotation;

/**
 *
 * Security annotation
 *
 * @Annotation
 */
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