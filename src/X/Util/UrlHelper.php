<?php
/**
 * URL helper class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Util;
final class UrlHelper
{

  /**
   * URL Without FileName
   * 
   * e.g:
   *   https://example.com                        -> https://example.com
   *   https://example.com?key=123                -> https://example.com
   *   https://example.com/abc.html               -> https://example.com
   *   https://example.com/abc.html?key=123       -> https://example.com
   *   https://example.com/abc/xyz.html           -> https://example.com/abc
   *   https://example.com/abc/xyz.html?key=123   -> http://example.com/abc
   *   //example.com                             -> //example.com
   *   //example.com/                            -> //example.com
   *   //example.com/abc.html                    -> //example.com
   *   //example.com/abc/xyz.html                -> //example.com/abc
   *   //example.com?key=123                     -> //example.com
   *   //example.com/?key=123                    -> //example.com
   *   //example.com/abc.html?key=123            -> //example.com
   *   //example.com/abc/xyz.html?key=123        -> //example.com/abc
   *   
   * @param  string $url
   * @return string
   */
  public static function getUrlWithoutFileName(string $url): string
  {
    $url = rtrim(strtok($url, '?'), '/');
    $urlInfo = parse_url($url);
    $scheme = !empty($urlInfo['scheme']) ? $urlInfo['scheme'] . '://' : '//';
    $path = $urlInfo['path'] ?? '';
    if (!empty($path)) {
      $pathInfo = pathinfo($path);
      if (isset($pathInfo['extension'])) {
        $path = rtrim(str_replace($pathInfo['filename'] . '.' . $pathInfo['extension'], '', $path), '/');
      }
    }
    return $scheme . $urlInfo['host'] . $path;
  }

  /**
   * URL home url
   * 
   * e.g:
   *   https://example.com                        -> https://example.com
   *   https://example.com?key=123                -> https://example.com
   *   https://example.com/abc.html               -> https://example.com
   *   https://example.com/abc.html?key=123       -> https://example.com
   *   https://example.com/abc/xyz.html           -> https://example.com
   *   https://example.com/abc/xyz.html?key=123   -> http://example.com
   *   //example.com                             -> //example.com
   *   //example.com/                            -> //example.com
   *   //example.com/abc.html                    -> //example.com
   *   //example.com/abc/xyz.html                -> //example.com
   *   //example.com?key=123                     -> //example.com
   *   //example.com/?key=123                    -> //example.com
   *   //example.com/abc.html?key=123            -> //example.com
   *   //example.com/abc/xyz.html?key=123        -> //example.com
   *   
   * @param  string $url
   * @return string
   */
  public static function getHomeUrl(string $url): string
  {
    $url = rtrim(strtok($url, '?'), '/');
    $urlInfo = parse_url($url);
    $scheme = !empty($urlInfo['scheme']) ? $urlInfo['scheme'] . '://' : '//';
    return $scheme . $urlInfo['host'];
  }
}