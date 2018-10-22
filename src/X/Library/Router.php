<?php
/**
 * Router Class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2018 Takuya Motoshima
 */
namespace X\Library;
abstract class Router extends \CI_Router
{

    // /**
    //  * Set request route
    //  *
    //  * Takes an array of URI segments as input and sets the class/method
    //  * to be called.
    //  *
    //  * @used-by CI_Router::_parse_routes()
    //  * @param   array   $segments   URI segments
    //  * @return  void
    //  */
    // protected function _set_request($segments = array())
    // {
    //     parent::_set_request(str_replace('-', '_', $segments));
    // }
}