<?php
/**
 * String util class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Util;
final class HtmlUtil
{

    /**
     * Get embed contents
     *
     * @param  string $url
     * @param  string $userIdentify
     * @return string
     */
    public static function get_embed_contents(string $url):string
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $contents = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($status >= 400 || $status < 200) {
            return '';
        }
        $contents = self::append_basetag($contents, $url);
        $charset = self::get_charset($contents);
        return [
            'contents' => $contents,
            'charset' => $charset
        ];
    }

    /**
     * Get charset
     *
     * @param string $contents
     * @return string
     */
    public static function get_charset(string $contents): string
    {
        if (preg_match('/<meta..*?charset=[\'"]?([\w-]+).*?>/is', $contents, $matches)) {
            return $matches[1];
        }
        return mb_detect_encoding($contents);
    }

    /**
     * Append basetag
     *
     * @param string $contents
     * @param string $url
     * @return string
     */
    public static function append_basetag(string $contents, string $url): string
    {
        // Remove comment
        $tmp = self::remove_comment($contents);

        // Remove invalid basetag
        if (preg_match_all('/<base.*?>/is', $tmp, $matches)) {
            $invalidBasetags = array_filter($matches[0], function($base) {
                return !preg_match('/<base..*?href=["\']\S+["\'].*?>/is', $base);
            });
            $tmp = str_replace($invalidBasetags, '', $tmp);
        }
        if (preg_match('/<base.*?>/is', $tmp)) {
            // When there is a valid basetag
            return $contents;
        }

        // Add basetag and return
        return preg_replace('/(<head.*?>)/is', '$1<base href="'. $url  . '">', $tmp);
    }

    /**
     * Append basetag
     *
     * @param string $contents
     * @return string
     */
    public static function remove_comment(string $contents): string
    {
        return preg_replace('/<!--.*?-->/s', '', $contents);
    }
}