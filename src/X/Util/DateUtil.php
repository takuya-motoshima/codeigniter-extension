<?php
/**
 * Date util class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2018 Takuya Motoshima
 */
namespace X\Util;
final class DateUtil
{

  /**
   * 
   * x年x月の第n週は、何日から何日までかを取得
   *
   * @param string $ym 対象年月。YYYYMM形式
   * @param int $nthWeek 取得したい週
   * @param bool $aroundMonth 前後の月を含めて算出する場合はtrue
   * @param bool $startMonday 月曜始まりとしたい場合はtrue
   * @return array
   */
  public static function getWeekPeriod(
    string $ym, 
    int $nthWeek, 
    bool $aroundMonth = false,
    bool $startMonday = true
  ): array
  {
    // 第1週最終曜日の計算用。デフォルトは土曜、月曜始まりの場合は日曜。
    $weekEnd = 6;
    if ($startMonday === true) {
      $weekEnd = 7;
    }

    // 月初(1日)の曜日を取得
    $weekNo = date('w', strtotime('first day of ' . $ym));

    // 曜日番号の差から第1週最終曜日の日付を算出
    $firstEndDay = $weekEnd - $weekNo + 1;
    if ($firstEndDay === 8) {
      // 1日が日曜かつ、月曜始まりの場合のみ8になってしまうので調整
      $firstEndDay = 1;
    }
    $firstEndStamp = strtotime($ym . sprintf('%02d', $firstEndDay));

    // 第1週最終曜日の日付 - 6 = 第1週の最初の日付
    $firstStartStamp = $firstEndStamp - (6 * 24 * 60 * 60);

    // 第1週の開始日と終了日が分かっているので、これを第N週にスライド
    $startStamp = $firstStartStamp + (($nthWeek - 1) * 7 * 24 * 60 * 60);
    $endStamp = $firstEndStamp + (($nthWeek - 1) * 7 * 24 * 60 * 60);

    if ($aroundMonth === false) {
      // 前後の月を含まない場合
      if (date('Ym', $startStamp) !== $ym) {
        // 週初めが先月の場合、1日に変換
        $startStamp = strtotime('first day of ' . $ym);
      }
      if (date('Ym', $endStamp) !== $ym) {
        // 週終わりが来月の場合、末日に変換
        $endStamp = strtotime('last day of ' . $ym);
      }
    }
    return [$startStamp, $endStamp];
    // return [date('Y-m-d', $startStamp), date('Y-m-d', $endStamp)];
  }
}