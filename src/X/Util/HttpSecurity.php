<?php
/**
 * HTTP security class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 */
namespace X\Util;
final class HttpSecurity {

  /**
   * Check if IP is allowed
   *
   *  e.g
   *    use \X\Util\HttpSecurity;
   *    HttpSecurity::isAllowIp('202.210.220.64',   '202.210.220.64/28');// false
   *    HttpSecurity::isAllowIp('202.210.220.65',   '202.210.220.64/28');// true
   *    HttpSecurity::isAllowIp('202.210.220.66',   '202.210.220.64/28');// true
   *    HttpSecurity::isAllowIp('202.210.220.78',   '202.210.220.64/28');// true
   *    HttpSecurity::isAllowIp('202.210.220.79',   '202.210.220.64/28');// false
   *    HttpSecurity::isAllowIp('202.210.220.80',   '202.210.220.64/28');// false
   *    HttpSecurity::isAllowIp('192.168.0.0',      '192.168.1.0/24');   // false
   *    HttpSecurity::isAllowIp('192.168.1.0',      '192.168.1.0/24');   // false
   *    HttpSecurity::isAllowIp('192.168.1.1',      '192.168.1.0/24');   // true
   *    HttpSecurity::isAllowIp('192.168.1.254',    '192.168.1.0/24');   // true
   *    HttpSecurity::isAllowIp('192.168.1.255',    '192.168.1.0/24');   // false
   *    HttpSecurity::isAllowIp('118.238.251.130',  '118.238.251.130');  // true
   *    HttpSecurity::isAllowIp('118.238.251.131',  '118.238.251.130');  // false
   * 
   * @param  string $ip
   * @param  string $accept
   * @return string
   */
  public static function isAllowIp(string $ip, string $accept): bool {

    if (strpos($accept, '/')) {
      list($net, $cidr) = explode("/", $accept);
    } else {
      // If there is no CIDR, compare the IP directly with the allowed IP
      return $ip === $accept;
    }

    // Not allowed if IP is Network address
    if ($net === $ip) {
      return false;
    }

    // Calculate broadcast address from CIDR
    $bcmask = ((1 << (32 - $cidr)) -1);
    $bc = long2ip(ip2long($ip) | $bcmask);
    //BroadcastAddressで来た場合は不許可
    if ($bc === $ip) {
      return false;
    }

    // Create a mask from CIDR
    $mask = ~((1 << (32 - $cidr)) -1);

    // Convert IP to decimal and perform bit operation with mask (and)
    $longIp = ip2long($ip);
    $filteredLong = $longIp & $mask;
    $longNet = ip2long($net);

    // IP is in range if the result is the same as the network address
    return ($filteredLong == $longNet);
  }
}