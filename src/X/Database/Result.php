<?php
/**
 * Database Result Trait
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Database;
trait Result
{

    /**
     * Query result. "array of the form KeyValue" version.
     *
     * @throws RuntimeException
     * @param  string $key = 'id'
     * @return  array
     */
    public function result_keyvalue(string $key = 'id'):array
    {
        $rows = parernt::result_array();
        if (empty($rows)) {
            return [];
        }
        if (array_key_exists($key, $rows[0]) === false) {
            throw new RuntimeException('result has no ' . $key . 'key');
        }
        return array_combine(array_column($rows, $key), $rows);
    }
}