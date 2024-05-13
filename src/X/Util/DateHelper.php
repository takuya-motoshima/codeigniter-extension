<?php
namespace X\Util;

/**
 * Date Utility.
 */
final class DateHelper {
  /**
   * Get the start and end dates of the week for the specified year and month.
   * @param string $ym The target date. YYYYMM format.
   * @param int $nthWeek Week number.
   * @param bool $aroundMonth (optional) If true, calculate including the months before and after.
   * @param bool $startMonday (optional) True if Monday is the first day of the week.
   * @return array Start and end dates of the week.
   */
  public static function getWeekPeriod(string $ym, int $nthWeek, bool $aroundMonth=false, bool $startMonday=true): array {
    $weekEnd = 6;
    if ($startMonday === true)
      $weekEnd = 7;
    $weekNo = date('w', strtotime('first day of ' . $ym));
    $firstEndDay = $weekEnd - $weekNo + 1;
    if ($firstEndDay === 8)
      $firstEndDay = 1;
    $firstEndStamp = strtotime($ym . sprintf('%02d', $firstEndDay));
    $firstStartStamp = $firstEndStamp - (6 * 24 * 60 * 60);
    $startStamp = $firstStartStamp + (($nthWeek - 1) * 7 * 24 * 60 * 60);
    $endStamp = $firstEndStamp + (($nthWeek - 1) * 7 * 24 * 60 * 60);
    if ($aroundMonth === false) {
      if (date('Ym', $startStamp) !== $ym)
        $startStamp = strtotime('first day of ' . $ym);
      if (date('Ym', $endStamp) !== $ym)
        $endStamp = strtotime('last day of ' . $ym);
    }
    return [$startStamp, $endStamp];
  }

  /**
   * Get all dates for a given month.
   * @param int $year Year.
   * @param int $month Month.
   * @param string $format The format of the date retrieved.
   * @return array List of dates.
   */
  public static function getDaysInMonth(int $year, int $month, string $format): array {
    $days = [];
    for($i=1, $date=\DateTime::createFromFormat('Y-n', "$year-$month"); $i<=$date->format('t'); $i++)
      $days[] = \DateTime::createFromFormat("Y-n-d", "$year-$month-$i")->format($format);
   return $days;
  }
}