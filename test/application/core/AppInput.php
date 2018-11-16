<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * AppInput
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima 
 */
class AppInput extends X\Library\Input {

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