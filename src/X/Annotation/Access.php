<?php
/**
 *
 * Method accessibility annotation
 *
 * @Annotation
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2018 Takuya Motoshima
 */
namespace X\Annotation;
use \Doctrine\Common\Annotations\Annotation;

class Access {
  public $allow_login = true;
  public $allow_logoff = true;
  public $allow_role = '';
}