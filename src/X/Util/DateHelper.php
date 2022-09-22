<?php
namespace X\Util;

final class DateHelper {
  /**
   * Returns the start and end dates of the week of the given year and month.
   *
   * @param string $yearMonth The target date. YYYYMM format
   * @param int $nthWeek Week number
   * @param bool $aroundMonth If true, calculate including the months before and after
   * @param bool $startMonday True if Monday is the first day of the week. 
   * @return array
   */
  public static function getWeekPeriod(string $yearMonth, int $nthWeek, bool $aroundMonth = false, bool $startMonday = true): array {
    $weekEnd = 6;
    if ($startMonday === true)
      $weekEnd = 7;
    $weekNo = date('w', strtotime('first day of ' . $yearMonth));
    $firstEndDay = $weekEnd - $weekNo + 1;
    if ($firstEndDay === 8)
      $firstEndDay = 1;
    $firstEndStamp = strtotime($yearMonth . sprintf('%02d', $firstEndDay));
    $firstStartStamp = $firstEndStamp - (6 * 24 * 60 * 60);
    $startStamp = $firstStartStamp + (($nthWeek - 1) * 7 * 24 * 60 * 60);
    $endStamp = $firstEndStamp + (($nthWeek - 1) * 7 * 24 * 60 * 60);
    if ($aroundMonth === false) {
      if (date('Ym', $startStamp) !== $yearMonth)
        $startStamp = strtotime('first day of ' . $yearMonth);
      if (date('Ym', $endStamp) !== $yearMonth)
        $endStamp = strtotime('last day of ' . $yearMonth);
    }
    return [$startStamp, $endStamp];
  }

  /**
   * Returns all dates in the specified month.
   *
   * @return array
   */
  public static function getDaysInMonth(int $year, int $month, string $format): array {
    $days = [];
    for($i=1, $date=\DateTime::createFromFormat('Y-n', "$year-$month"); $i<=$date->format('t'); $i++)
      $days[] = \DateTime::createFromFormat("Y-n-d", "$year-$month-$i")->format($format);
   return $days;
  }
}