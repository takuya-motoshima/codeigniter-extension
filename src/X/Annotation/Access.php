<?php
namespace X\Annotation;
// The "\Doctrine\Common\Annotations\Annotation" must be read before the class comment.
use \Doctrine\Common\Annotations\Annotation;

/**
 *
 * Method accessibility annotation
 *
 * @Annotation
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2018 Takuya Motoshima
 */
class Access {
  public $allow_login = true;
  public $allow_logoff = true;
  public $allow_role = '';
}