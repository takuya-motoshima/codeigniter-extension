<?php
namespace X\Model;

abstract class SessionModel implements SessionModelInterface {
  const SESSION_NAME = 'user';

  /**
   * Callback set session.
   *
   * @param string  $id
   * @return void
   */
  abstract protected static function getUser(string $id): array;

  /**
   * Set session.
   *
   * @param string $id It is ID if there is only one argument, column name if there are two arguments
   * @param mixed $value
   * @return string
   */
  public final static function set(string $id, $value = null): string {
    if (count(func_get_args()) === 1)
      $_SESSION[self::SESSION_NAME] = static::getUser($id);
    else {
      $field = $id;
      if (!array_key_exists($field, $_SESSION[self::SESSION_NAME]))
        throw new \RuntimeException($field . ' column does not exist');
      $_SESSION[self::SESSION_NAME][$field] = $value;
    }
    return get_called_class();
  }

  /**
   *
   * Unset session.
   * 
   * @return string
   */
  public final static function unset(): string {
    unset($_SESSION[self::SESSION_NAME]);
    return get_called_class();
  }

  /**
   * Isset session .
   * 
   * @return void
   */
  public final static function isset(): bool {
    return isset($_SESSION[self::SESSION_NAME]);
  }

  /**
   * Get session.
   * 
   * @return stdClass|string
   */
  public final static function get(string $field = null) {
    if (!self::isset())
      return null;
    $user = json_decode(json_encode($_SESSION[self::SESSION_NAME]));
    return empty($field) ? $user : $user->$field;
  }
}
