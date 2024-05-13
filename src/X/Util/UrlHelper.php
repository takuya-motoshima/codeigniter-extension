<?php
namespace X\Util;

/**
 * URL Utility.
 */
final class UrlHelper {
  /**
   * Remove the filename portion from the URL.
   * ```php
   * use \X\Util\UrlHelper;
   *
   * UrlHelper::withoutFile('https://abc.com');// => "https://abc.com"
   * UrlHelper::withoutFile('https://abc.com?name=foo');// => "https://abc.com"
   * UrlHelper::withoutFile('https://abc.com/index.html');// => "https://abc.com"
   * UrlHelper::withoutFile('https://abc.com/index.html?name=foo');// => "https://abc.com"
   * UrlHelper::withoutFile('https://abc.com/def/index.html');// => "https://abc.com/abc"
   * UrlHelper::withoutFile('https://abc.com/def/index.html?name=foo');// => "http://abc.com/abc"
   * UrlHelper::withoutFile('//abc.com');// => "//abc.com"
   * UrlHelper::withoutFile('//abc.com/');// => "//abc.com"
   * UrlHelper::withoutFile('//abc.com/index.html');// => "//abc.com"
   * UrlHelper::withoutFile('//abc.com/def/index.html');// => "//abc.com/abc"
   * UrlHelper::withoutFile('//abc.com?name=foo');// => "//abc.com"
   * UrlHelper::withoutFile('//abc.com/?name=foo');// => "//abc.com"
   * UrlHelper::withoutFile('//abc.com/index.html?name=foo');// => "//abc.com"
   * UrlHelper::withoutFile('//abc.com/def/index.html?name=foo');// => "//abc.com/abc"
   * ```
   * @param string $url URL.
   * @return string URL with filenames stripped.
   */
  public static function withoutFile(string $url): string {
    $url = rtrim(strtok($url, '?'), '/');
    $urlInfo = parse_url($url);
    $scheme = !empty($urlInfo['scheme']) ? $urlInfo['scheme'] . '://' : '//';
    $path = $urlInfo['path'] ?? '';
    if (!empty($path)) {
      $pathInfo = pathinfo($path);
      if (isset($pathInfo['extension']))
        $path = rtrim(str_replace($pathInfo['filename'] . '.' . $pathInfo['extension'], '', $path), '/');
    }
    return $scheme . $urlInfo['host'] . $path;
  }

  /**
   * Extract only the protocol and origin from the URL.
   * ```php
   * use \X\Util\UrlHelper;
   *
   * UrlHelper::domain('https://abc.com');// "https://abc.com"
   * UrlHelper::domain('https://abc.com?name=foo');// "https://abc.com"
   * UrlHelper::domain('https://abc.com/index.html');// "https://abc.com"
   * UrlHelper::domain('https://abc.com/index.html?name=foo');// "https://abc.com"
   * UrlHelper::domain('https://abc.com/def/index.html');// "https://abc.com"
   * UrlHelper::domain('https://abc.com/def/index.html?name=foo');// "http://abc.com"
   * UrlHelper::domain('//abc.com');// "//abc.com"
   * UrlHelper::domain('//abc.com/');// "//abc.com"
   * UrlHelper::domain('//abc.com/index.html');// "//abc.com"
   * UrlHelper::domain('//abc.com/def/index.html');// "//abc.com"
   * UrlHelper::domain('//abc.com?name=foo');// "//abc.com"
   * UrlHelper::domain('//abc.com/?name=foo');// "//abc.com"
   * UrlHelper::domain('//abc.com/index.html?name=foo');// "//abc.com"
   * UrlHelper::domain('//abc.com/def/index.html?name=foo');// "//abc.com"
   * ```
   * @param string $url URL.
   * @return string Protocol and origin taken from the URL.
   */
  public static function domain(string $url): string {
    $url = rtrim(strtok($url, '?'), '/');
    $urlInfo = parse_url($url);
    $scheme = !empty($urlInfo['scheme']) ? $urlInfo['scheme'] . '://' : '//';
    return $scheme . $urlInfo['host'];
  }
}