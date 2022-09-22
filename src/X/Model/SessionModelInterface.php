<?php
namespace X\Model;

interface SessionModelInterface {
  /**
    * Set session.
    *
    * @param string  $id It is ID if there is only one argument, column name if there are two arguments
    * @param mixed $value
    * @return string
    */
  public static function set(string $id, $value = null): string;

  /**
    * Unset session .
    * 
    * @return string
    */
  public static function unset(): string;

  /**
    *
    * Isset session.
    * 
    * @return void
    */
  public static function isset(): bool;

  /**
    *
    * Get session.
    * 
    * @return stdClass|string
    */
  public static function get(string $field = null);
}