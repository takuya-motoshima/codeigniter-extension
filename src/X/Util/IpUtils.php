<?php

/**
 * IP utility class.
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Util;
use \X\Util\Logger;

final class IpUtils {

  private static $checkedIps = [];

  /**
   * Returns true for IPv4 format.
   *
   * @example
   * // IPv4 format check.
   * IpUtils::isIPv4('234.192.0.2');// true
   * IpUtils::isIPv4('234.198.51.100');// true
   * IpUtils::isIPv4('234.203.0.113');// true
   * IpUtils::isIPv4('0000:0000:0000:0000:0000:ffff:7f00:0001');// false
   * IpUtils::isIPv4('::1');// false
   * 
   * @param  string  $ip
   * @return boolean
   */
  public static function isIPv4(string $ip): bool {
    return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
  }

  /**
   * Returns true for IPv6 format.
   *
   * @example
   * // IPv6 format check.
   * IpUtils::isIPv6('234.192.0.2');// false
   * IpUtils::isIPv6('234.198.51.100');// false
   * IpUtils::isIPv6('234.203.0.113');// false
   * IpUtils::isIPv6('0000:0000:0000:0000:0000:ffff:7f00:0001');// true
   * IpUtils::isIPv6('::1');// true
   * 
   * @param  string  $ip
   * @return boolean
   */
  public static function isIPv6(string $ip): bool {
    return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
  }

  /**
   * Checks if an IPv4 or IPv6 address is contained in the list of given IPs or subnets.
   *
   * @example
   * // IP range check.
   * // 202.210.220.64/28
   * IpUtils::inRange('202.210.220.63', '202.210.220.64/28');// false
   * IpUtils::inRange('202.210.220.64', '202.210.220.64/28');// true
   * IpUtils::inRange('202.210.220.65', '202.210.220.64/28');// true
   * IpUtils::inRange('202.210.220.78', '202.210.220.64/28');// true
   * IpUtils::inRange('202.210.220.79', '202.210.220.64/28');// true
   * IpUtils::inRange('202.210.220.80', '202.210.220.64/28');// false
   * 
   * // 192.168.1.0/24
   * IpUtils::inRange('192.168.0.255', '192.168.1.0/24'); // false
   * IpUtils::inRange('192.168.1.0', '192.168.1.0/24'); // true
   * IpUtils::inRange('192.168.1.1', '192.168.1.0/24'); // true
   * IpUtils::inRange('192.168.1.244', '192.168.1.0/24'); // true
   * IpUtils::inRange('192.168.1.255', '192.168.1.0/24'); // true
   * IpUtils::inRange('192.168.2.0', '192.168.1.0/24'); // false
   * 
   * // 118.238.251.130
   * IpUtils::inRange('118.238.251.129', '118.238.251.130'); // false
   * IpUtils::inRange('118.238.251.130', '118.238.251.130'); // true
   * IpUtils::inRange('118.238.251.131', '118.238.251.130'); // false
   * 
   * // 118.238.251.130/32
   * IpUtils::inRange('118.238.251.129', '118.238.251.130/32'); // false
   * IpUtils::inRange('118.238.251.130', '118.238.251.130/32'); // true
   * IpUtils::inRange('118.238.251.131', '118.238.251.130/32'); // false
   * 
   * // 2001:4860:4860::8888/32
   * IpUtils::inRange('2001:4859:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF', '2001:4860:4860::8888/32');// false
   * IpUtils::inRange('2001:4860:4860:0000:0000:0000:0000:8888', '2001:4860:4860::8888/32');// true
   * IpUtils::inRange('2001:4860:4860:0000:0000:0000:0000:8889', '2001:4860:4860::8888/32');// true
   * IpUtils::inRange('2001:4860:FFFF:FFFF:FFFF:FFFF:FFFF:FFFE', '2001:4860:4860::8888/32');// true
   * IpUtils::inRange('2001:4860:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF', '2001:4860:4860::8888/32');// true
   * IpUtils::inRange('2001:4861:0000:0000:0000:0000:0000:0000', '2001:4860:4860::8888/32');// false
   * 
   * // 2404:7a81:b0a0:9100::/64
   * IpUtils::inRange('2404:7A81:B0A0:90FF:0000:0000:0000:0000', '2404:7A81:B0A0:9100::/64');// false
   * IpUtils::inRange('2404:7A81:B0A0:9100:0000:0000:0000:0000', '2404:7A81:B0A0:9100::/64');// true
   * IpUtils::inRange('2404:7A81:B0A0:9100:0000:0000:0000:0001', '2404:7A81:B0A0:9100::/64');// true
   * IpUtils::inRange('2404:7A81:B0A0:9100:A888:5EE2:EA92:B618', '2404:7A81:B0A0:9100::/64');// true
   * IpUtils::inRange('2404:7A81:B0A0:9100:D03:959E:7F47:9B77', '2404:7A81:B0A0:9100::/64');// true
   * IpUtils::inRange('2404:7A81:B0A0:9100:FFFF:FFFF:FFFF:FFFE', '2404:7A81:B0A0:9100::/64');// true
   * IpUtils::inRange('2404:7A81:B0A0:9100:FFFF:FFFF:FFFF:FFFF', '2404:7A81:B0A0:9100::/64');// true
   * IpUtils::inRange('2404:7A81:B0A0:9101:0000:0000:0000:0000', '2404:7A81:B0A0:9100::/64');// false
   * 
   * @param string|array $ips List of IPs or subnets (can be a string if only a single one)
   * @return bool Whether the IP is valid
   */
  public static function inRange(string $requestIp, $ips): bool {
    if (!\is_array($ips)) $ips = [$ips];
    $method = substr_count($requestIp, ':') > 1 ? 'inRangeIPv6' : 'inRangeIPv4';
    foreach ($ips as $ip) {
      if ((substr_count($requestIp, ':') > 1) !== (substr_count($ip, ':') > 1)) continue;
      if (self::$method($requestIp, $ip)) return true;
    }
    return false;
  }

  /**
   * Compares two IPv4 addresses.
   * In case a subnet is given, it checks if it contains the request IP.
   * 
   * @example
   * // IPv4 range check.
   * // 202.210.220.64/28
   * IpUtils::inRangeIPv4('202.210.220.63', '202.210.220.64/28')// false
   * IpUtils::inRangeIPv4('202.210.220.64', '202.210.220.64/28')// true
   * IpUtils::inRangeIPv4('202.210.220.65', '202.210.220.64/28')// true
   * IpUtils::inRangeIPv4('202.210.220.78', '202.210.220.64/28')// true
   * IpUtils::inRangeIPv4('202.210.220.79', '202.210.220.64/28')// true
   * IpUtils::inRangeIPv4('202.210.220.80', '202.210.220.64/28')// false
   * 
   * // 192.168.1.0/24
   * IpUtils::inRangeIPv4('192.168.0.255', '192.168.1.0/24');// false
   * IpUtils::inRangeIPv4('192.168.1.0', '192.168.1.0/24');// true
   * IpUtils::inRangeIPv4('192.168.1.1', '192.168.1.0/24');// true
   * IpUtils::inRangeIPv4('192.168.1.244', '192.168.1.0/24');// true
   * IpUtils::inRangeIPv4('192.168.1.255', '192.168.1.0/24');// true
   * IpUtils::inRangeIPv4('192.168.2.0', '192.168.1.0/24');// false
   * 
   * // 118.238.251.130/32
   * IpUtils::inRangeIPv4('118.238.251.129', '118.238.251.130/32');// false
   * IpUtils::inRangeIPv4('118.238.251.130', '118.238.251.130/32');// true
   * IpUtils::inRangeIPv4('118.238.251.131', '118.238.251.130/32');// false
   * 
   * // 118.238.251.130
   * IpUtils::inRangeIPv4('118.238.251.129', '118.238.251.130');// false
   * IpUtils::inRangeIPv4('118.238.251.130', '118.238.251.130');// true
   * IpUtils::inRangeIPv4('118.238.251.131', '118.238.251.130');// false
   * 
   * @param string $ip IPv4 address or subnet in CIDR notation
   * @return bool Whether the request IP matches the IP, or whether the request IP is within the CIDR subnet
   */
  public static function inRangeIPv4(string $requestIp, string $ip): bool {
    $cacheKey = $requestIp.'-'.$ip;
    if (isset(self::$checkedIps[$cacheKey])) return self::$checkedIps[$cacheKey];
    if (!filter_var($requestIp, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV4)) return self::$checkedIps[$cacheKey] = false;
    if (false !== strpos($ip, '/')) {
      list($address, $netmask) = explode('/', $ip, 2);
      if ('0' === $netmask) return self::$checkedIps[$cacheKey] = filter_var($address, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV4);
      if ($netmask < 0 || $netmask > 32) return self::$checkedIps[$cacheKey] = false;
    } else {
      $address = $ip;
      $netmask = 32;
    }
    if (false === ip2long($address)) return self::$checkedIps[$cacheKey] = false;
    return self::$checkedIps[$cacheKey] = 0 === substr_compare(sprintf('%032b', ip2long($requestIp)), sprintf('%032b', ip2long($address)), 0, $netmask);
  }

  /**
   * Compares two IPv6 addresses.
   * In case a subnet is given, it checks if it contains the request IP.
   *
   * @example
   * // IPv6 range check.
   * IpUtils::inRangeIPv6('2404:7A81:B0A0:90FF:0000:0000:0000:0000', '2404:7A81:B0A0:9100::/64');// false
   * IpUtils::inRangeIPv6('2404:7A81:B0A0:9100:0000:0000:0000:0000', '2404:7A81:B0A0:9100::/64');// true
   * IpUtils::inRangeIPv6('2404:7A81:B0A0:9100:0000:0000:0000:0001', '2404:7A81:B0A0:9100::/64');// true
   * IpUtils::inRangeIPv6('2404:7A81:B0A0:9100:A888:5EE2:EA92:B618', '2404:7A81:B0A0:9100::/64');// true
   * IpUtils::inRangeIPv6('2404:7A81:B0A0:9100:D03:959E:7F47:9B77', '2404:7A81:B0A0:9100::/64');// true
   * IpUtils::inRangeIPv6('2404:7A81:B0A0:9100:FFFF:FFFF:FFFF:FFFE', '2404:7A81:B0A0:9100::/64');// true
   * IpUtils::inRangeIPv6('2404:7A81:B0A0:9100:FFFF:FFFF:FFFF:FFFF', '2404:7A81:B0A0:9100::/64');// true
   * IpUtils::inRangeIPv6('2404:7A81:B0A0:9101:0000:0000:0000:0000', '2404:7A81:B0A0:9100::/64');// false
   * 
   * @param string $ip IPv6 address or subnet in CIDR notation
   * @return bool Whether the IP is valid
   */
  public static function inRangeIPv6(string $requestIp, string $ip): bool {
    $cacheKey = $requestIp.'-'.$ip;
    if (isset(self::$checkedIps[$cacheKey]))
      return self::$checkedIps[$cacheKey];
    if (!((\extension_loaded('sockets') && \defined('AF_INET6')) || @inet_pton('::1')))
      throw new \RuntimeException('Unable to check Ipv6. Check that PHP was not compiled with option "disable-ipv6".');
    if (false !== strpos($ip, '/')) {
      list($address, $netmask) = explode('/', $ip, 2);
      if ('0' === $netmask) return (bool) unpack('n*', @inet_pton($address));
      if ($netmask < 1 || $netmask > 128) return self::$checkedIps[$cacheKey] = false;
    } else {
      $address = $ip;
      $netmask = 128;
    }
    $bytesAddr = unpack('n*', @inet_pton($address));
    $bytesTest = unpack('n*', @inet_pton($requestIp));
    if (!$bytesAddr || !$bytesTest) return self::$checkedIps[$cacheKey] = false;
    for ($i = 1, $ceil = ceil($netmask / 16); $i <= $ceil; ++$i) {
      $left = $netmask - 16 * ($i - 1);
      $left = ($left <= 16) ? $left : 16;
      $mask = ~(0xffff >> $left) & 0xffff;
      if (($bytesAddr[$i] & $mask) != ($bytesTest[$i] & $mask)) return self::$checkedIps[$cacheKey] = false;
    }
    return self::$checkedIps[$cacheKey] = true;
  }

  /**
   * Get client ip from X-Forwarded-For.
   *
   * @example
   * // Get client ip.
   * IpUtils::getClientIpFromXFF();//  202.210.220.78
   * 
   * @return string
   */
  public static function getClientIpFromXFF() :?string {
    return !empty($_SERVER['HTTP_X_FORWARDED_FOR'])
      ? explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]
      : null;
  }
}