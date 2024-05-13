<?php
namespace X\Annotation;
use \Doctrine\Common\Annotations\Annotation;

/**
 * Controller method access permission information.
 * @Annotation
 */
class Access {
  /**
   * Set to true to allow access for logged-in users or false to disallow access.
   * @var bool
   */
  public $allow_login = true;

  /**
   * Set to true to allow access for logoff users, or false to disallow access.
   * @var bool
   */
  public $allow_logoff = true;

  /**
   * Role names of logged-in users to be allowed access. You can specify multiple comma-separated names.
   * @var string
   */
  public $allow_role = '';

  /**
   * Set to true to allow access from HTTP, false to disallow. For example, if you want to allow access only from the CLI, set false.
   * @var bool 
   */
  public $allow_http = true;
}