<?php
/**
 * Array util class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2018 Takuya Motoshima
 */
namespace X\Util;
final class ArrayUtil
{

    /**
     * Filter key
     *
     * @param  array $arr
     * @param  array $allowed
     * @return array
     */
    public static function filter_key(array $arr, ...$allowed):array
    {
        return array_filter($arr, function ($key) use ($allowed) {
            return in_array($key, $allowed);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Reset key
     *
     * @param  array $arr
     * @return array
     */
    public static function reset_key(array $arr):array
    {
        return array_values($arr);
    }
}