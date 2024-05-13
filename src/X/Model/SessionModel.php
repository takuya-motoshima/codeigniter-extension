<?php
namespace X\Model;

/**
 * Session management model.
 */
abstract class SessionModel implements SessionModelInterface {
  /**
   * Key name of the session to store user information.
   * @var string
   */
  const SESSION_NAME = 'user';

  /**
   * Callback set session.
   * @param string $id User ID.
   * @return array User data.
   */
  abstract protected static function getUser(string $id): array;

  /**
   * Set session.
   * @param string $id It is ID if there is only one argument, column name if there are two arguments.
   * @param mixed $value Set value.
   * @return string Subclass Name.
   */
  public final static function set(string $id, $value=null): string {
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
   * Session discarded.
   * @return string Subclass Name.
   */
  public final static function unset(): string {
    unset($_SESSION[self::SESSION_NAME]);
    return get_called_class();
  }

  /**
   * Isset session.
   * @return bool Whether the session exists.
   */
  public final static function isset(): bool {
    return isset($_SESSION[self::SESSION_NAME]);
  }

  /**
   * Get session.
   * @param string $field (optional) If you want to retrieve only a specific field from the session, specify the name of that field.
   * @return stdClass|string Session data.
   */
  public final static function get(string $field=null) {
    if (!self::isset())
      return null;
    $user = json_decode(json_encode($_SESSION[self::SESSION_NAME]));
    return empty($field) ? $user : $user->$field;
  }
}
