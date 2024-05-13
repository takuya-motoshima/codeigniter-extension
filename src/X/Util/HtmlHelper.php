<?php
namespace X\Util;
use \X\Util\Logger;

/**
 * HTML Utility.
 */
final class HtmlHelper {
  /**
   * Get content for a given URL.
   * @param string $url The URL of the page.
   * @return object{contents: string, charset: string}|null Acquisition Result.
   */
  public static function getContentForIframe(string $url): ?\stdClass {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $content = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($status >= 400 || $status < 200)
      return '';
    $content = self::appendHeadBaseTag($content, $url);
    $charset = self::getContentCharset($content);
    return json_decode(json_encode([
      'contents' => $content,
      'charset' => $charset
    ]));
  }

  /**
   * Get character encoding from HTML
   * @param string $content HTML Content.
   * @return string Character code.
   */
  public static function getContentCharset(string $content): string {
    if (preg_match('/<meta..*?charset=[\'"]?([\w-]+).*?>/is', $content, $matches))
      return $matches[1];
    return mb_detect_encoding($content);
  }

  /**
   * Add <base /> tag to HTML content.
   * @param string $content HTML Content.
   * @param string $url URL of the <base /> tag.
   * @return string HTML.
   */
  public static function appendHeadBaseTag(string $content, string $url): string {
    $tmp = self::removeHtmlComment($content);
    if (preg_match_all('/<base.*?>/is', $tmp, $matches)) {
      $invalidBasetags = array_filter($matches[0], function($base) {
        return !preg_match('/<base..*?href=["\']\S+["\'].*?>/is', $base);
      });
      $tmp = str_replace($invalidBasetags, '', $tmp);
    }
    if (preg_match('/<base.*?>/is', $tmp))
      return $content;
    return preg_replace('/(<head.*?>)/is', '$1<base href="'. $url  . '">', $tmp);
  }

  /**
   * Remove HTML comments.
   * @param string $content HTML Content.
   * @return string HTML.
   */
  public static function removeHtmlComment(string $content): string {
    return preg_replace('/<!--.*?-->/s', '', $content);
  }

  /**
   * Get page title from HTML.
   * @param \DOMDocument $dom DOMDocument instance.
   * @return string|null Page Title.
   */
  public static function getTitle(\DOMDocument &$dom): ?string {
    $head = self::getHeadNode($dom);
    $title = $head->getElementsByTagName('title');
    if ($title->length === 0)
      return null;
    return $title->item(0)->textContent;
  }

  /**
   * Get body node.
   * @param \DOMDocument $dom DOMDocument instance.
   * @return \DOMElement body node.
   */
  public static function getBodyNode(\DOMDocument &$dom): \DOMElement {
    return $dom->getElementsByTagName('body')->item(0);
  }

  /**
   * Get head node.
   * @param \DOMDocument $dom DOMDocument instance.
   * @return \DOMElement head node.
   */
  public static function getHeadNode(\DOMDocument &$dom): \DOMElement {
    return $dom->getElementsByTagName('head')->item(0);
  }

  /**
   * Get dom document.
   * @param string $url HTML page URL.
   * @param \DOMDocument $dom DOMDocument instance.
   * @return void
   */
  public static function getDomDocument(string $url, &$dom): void {
    $content = @file_get_contents($url, false, stream_context_create(['http' => ['ignore_errors' => true]]));
    if (strpos($http_response_header[0], '200') === false && strpos($http_response_header[0], '301') === false)
      return;
    $dom = new \DOMDocument();
    libxml_use_internal_errors(true);
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadHTML('<?xml encoding="UTF-8">' . $content);
    // $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'utf-8'));
    // $dom->loadHTML(file_get_contents($url));
    libxml_clear_errors(); 
  }
}