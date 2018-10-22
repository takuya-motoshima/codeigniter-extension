<?php
/**
 * Input Class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2018 Takuya Motoshima
 */
namespace X\Library;
abstract class Input extends \CI_Input
{

    /**
     * Fetch an item from the PUT array
     *
     * @param   mixed   $index      Index for item to be fetched from $_PUT
     * @param   bool    $xss_clean  Whether to apply XSS filtering
     * @return  mixed
     */
    public function put($index = NULL, $xss_clean = NULL)
    {
        return parent::input_stream($index, $xss_clean);
    }

    /**
     * Fetch an item from the DELETE array
     *
     * @param   mixed   $index      Index for item to be fetched from $_DELETE
     * @param   bool    $xss_clean  Whether to apply XSS filtering
     * @return  mixed
     */
    public function delete($index = NULL, $xss_clean = NULL)
    {
        return parent::input_stream($index, $xss_clean);
    }
}