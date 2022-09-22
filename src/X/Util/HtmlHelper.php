<?php
namespace X\Util;
use \X\Util\Logger;

final class HtmlHelper {
  /**
   * Get content for IFrame.
   */
  public static function getContentForIframe(string $url): ?\stdClass {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    $contents = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($status >= 400 || $status < 200)
      return '';
    $contents = self::appendHeadBaseTag($contents, $url);
    $charset = self::getContentCharset($contents);
    return json_decode(json_encode([
      'contents' => $contents,
      'charset' => $charset
    ]));
  }

  /**
   * Get charset.
   */
  public static function getContentCharset(string $contents): string {
    if (preg_match('/<meta..*?charset=[\'"]?([\w-]+).*?>/is', $contents, $matches))
      return $matches[1];
    return mb_detect_encoding($contents);
  }

  /**
   * Append basetag.
   */
  public static function appendHeadBaseTag(string $contents, string $url): string {
    $tmp = self::removeHtmlComment($contents);
    if (preg_match_all('/<base.*?>/is', $tmp, $matches)) {
      $invalidBasetags = array_filter($matches[0], function($base) {
        return !preg_match('/<base..*?href=["\']\S+["\'].*?>/is', $base);
      });
      $tmp = str_replace($invalidBasetags, '', $tmp);
    }
    if (preg_match('/<base.*?>/is', $tmp))
      return $contents;
    return preg_replace('/(<head.*?>)/is', '$1<base href="'. $url  . '">', $tmp);
  }

  /**
   * Append basetag.
   */
  public static function removeHtmlComment(string $contents): string {
    return preg_replace('/<!--.*?-->/s', '', $contents);
  }

  /**
   * Get title.
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
   */
  public static function getBodyNode(\DOMDocument &$dom): \DOMElement {
    return $dom->getElementsByTagName('body')->item(0);
  }

  /**
   * Get head node.
   */
  public static function getHeadNode(\DOMDocument &$dom): \DOMElement {
    return $dom->getElementsByTagName('head')->item(0);
  }

  /**
   * Get dom document.
   */
  public static function getDomDocument(string $url, &$dom) {
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