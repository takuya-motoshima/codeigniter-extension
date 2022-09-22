<?php
namespace X\Annotation;
use \Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class Access {
  public $allow_login = true;
  public $allow_logoff = true;
  public $allow_role = '';
  public $allow_http = true;
}