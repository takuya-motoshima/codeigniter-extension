<?php
/**
 * Date helper class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2018 Takuya Motoshima
 */
namespace X\Util;

final class DateHelper {

  /**
   * Returns the start and end dates of the week of the given year and month
   *
   * @param string $yearMonth The target date. YYYYMM format
   * @param int $nthWeek Week number
   * @param bool $aroundMonth If true, calculate including the months before and after
   * @param bool $startMonday True if Monday is the first day of the week. 
   * @return array
   */
  public static function getWeekPeriod(string $yearMonth, int $nthWeek, bool $aroundMonth = false, bool $startMonday = true): array {
    // Last day of the week.The default is Saturday.If Monday is the first day of the week, it will be Sunday.
    $weekEnd = 6;
    if ($startMonday === true) $weekEnd = 7;

    // Day of the first day of the month
    $weekNo = date('w', strtotime('first day of ' . $yearMonth));

    // Date of last day of week 1
    $firstEndDay = $weekEnd - $weekNo + 1;

    // If the first day of a month is Sunday and the first day of the week is Monday, the value will be 8, so adjust it.
    if ($firstEndDay === 8) $firstEndDay = 1;
    $firstEndStamp = strtotime($yearMonth . sprintf('%02d', $firstEndDay));

    // Date of last day of first week-6 = first date of first week
    $firstStartStamp = $firstEndStamp - (6 * 24 * 60 * 60);

    // Since we know the start and end dates of week 1, slide to week N
    $startStamp = $firstStartStamp + (($nthWeek - 1) * 7 * 24 * 60 * 60);
    $endStamp = $firstEndStamp + (($nthWeek - 1) * 7 * 24 * 60 * 60);
    if ($aroundMonth === false) {
      // When the previous and next months are not included

      // If the first day of the week is last month, convert to the first day
      if (date('Ym', $startStamp) !== $yearMonth) $startStamp = strtotime('first day of ' . $yearMonth);

      // If the end of the week is the next month, convert to the last day
      if (date('Ym', $endStamp) !== $yearMonth) $endStamp = strtotime('last day of ' . $yearMonth);
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