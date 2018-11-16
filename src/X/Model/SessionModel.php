<?php
/**
 * Session model class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 * @property CI_DB_query_builder $db
 */
namespace X\Model;
use X\Util\Logger;
abstract class SessionModel implements SessionModelInterface
{

    const SESSION_NAME = 'user';

    /**
     *
     * Callback set session 
     *
     * @param string  $id
     * @return void
     */
    abstract protected static function get_data(string $id): array;

    /**
     *
     * Set session 
     *
     * @param string $id It is ID if there is only one argument, column name if there are two arguments
     * @param mixed $value
     * @return string
     */
    public final static function set(string $id, $value = null): string
    {
        if (count(func_get_args()) === 1) {
            $_SESSION[self::SESSION_NAME] = static::get_data($id);
            // $that = get_called_class();
            // $_SESSION[self::SESSION_NAME] = $that::get_data($id);
        } else {
            $column = $id;
            if (!array_key_exists($column, $_SESSION[self::SESSION_NAME])) {
                throw new \RuntimeException($column . ' column does not exist');
            }
            Logger::i('Session before update=', $_SESSION[self::SESSION_NAME]);
            $_SESSION[self::SESSION_NAME][$column] = $value;
            Logger::i('Session after update=', $_SESSION[self::SESSION_NAME]);
        }
        return get_called_class();
    }

    /**
     *
     * Unset session 
     * 
     * @return string
     */
    public final static function unset(): string
    {
        unset($_SESSION[self::SESSION_NAME]);
        return get_called_class();
    }

    /**
     *
     * Isset session 
     * 
     * @return void
     */
    public final static function isset(): bool
    {
        return isset($_SESSION[self::SESSION_NAME]);
    }

    /**
     *
     * Get session
     * 
     * @return stdClass|string
     */
    public final static function get(string $column = null)
    // public final static function get(string $column = null): ?\stdClass
    {
        if (!self::isset()) {
            return null;
        }
        $user = json_decode(json_encode($_SESSION[self::SESSION_NAME]));
        return empty($column) ? $user : $user->$column;
    }
}
