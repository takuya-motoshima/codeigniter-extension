<?php
namespace X\Model;

/**
 * Session management model interface.
 */
interface SessionModelInterface {
  /**
   * Set session.
   * @param string $id It is ID if there is only one argument, column name if there are two arguments.
   * @param mixed $value Set value.
   * @return string Subclass Name.
   */
  public static function set(string $id, $value=null): string;

  /**
   * Session discarded.
   * @return string Subclass Name.
   */
  public static function unset(): string;

  /**
   * Isset session.
   * @return bool Whether the session exists.
   */
  public static function isset(): bool;

  /**
   * Get session.
   * @param string $field (optional) If you want to retrieve only a specific field from the session, specify the name of that field.
   * @return stdClass|string Session data.
   */
  public static function get(string $field=null);
}