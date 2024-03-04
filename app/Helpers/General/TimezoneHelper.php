<?php

namespace App\Helpers\General;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use DateTime;
use IntlDateFormatter;

/**
 * Class Timezone.
 */
class TimezoneHelper
{
    const DATE_WITH_WEEK_DAY = 1;
    const DATE_WITH_AGE = 2;
    const DATE_WITH_MONTH = 3;
    const DATE_WIT_MONTH_AT_PAGINATOR = 4;
    const DATE_WIT_RANGE_TIME = 5;
    const DATE_WITHOUT_YEAR = 6;
    const TIME_ZONE = 'ja_JP';
    const TEXT_AGE = '才';
    const TIME_WITHOUT_DATE = 7;
    const DATE_WEEK = 8;
    const DATE_WITH_TIME = 9;
    const DATE_WITH_WEEK_DAY_SLASH = 10;
    const TIME_WITHOUT_DATE_MARK = 11;

    /**
     * @param Carbon $date
     * @param string $format
     *
     * @return Carbon
     */
    public function convertToLocal(Carbon $date, $format = 'D M j G:i:s T Y'): string
    {
        return $date->setTimezone(auth()->user()->timezone ?? config('app.timezone'))->format($format);
    }

    /**
     * @param $date
     *
     * @return Carbon
     */
    public function convertFromLocal($date): Carbon
    {
        return Carbon::parse($date, auth()->user()->timezone)->setTimezone('UTC');
    }

    public function convertDateToString($date, $dateFormat = 0)
    {
        if (!$date) {
            return null;
        }
        $strDate = date_format(date_create($date), 'Y年m月d日');

        switch ($dateFormat) {
            case self::DATE_WIT_MONTH_AT_PAGINATOR:
                $strDate = date_format(date_create($date), 'm月度');
                break;
            case self::DATE_WITH_MONTH:
                $strDate = date_format(date_create($date), 'Y年 m月');
                break;
            case self::DATE_WITH_WEEK_DAY:
                $weekdayFormatter = new IntlDateFormatter(
                    self::TIME_ZONE,
                    IntlDateFormatter::NONE,
                    IntlDateFormatter::NONE,
                    date_default_timezone_get(),
                    IntlDateFormatter::GREGORIAN,
                    'EEEEE' // weekday in one letter
                );
                $datetime = new DateTime($date);
                $weekday =  $weekdayFormatter->format($datetime);

                $strDate = $strDate . '（' . $weekday . '）';
                break;
            case self::DATE_WITH_AGE:
                $now = new DateTime();
                $interval = $now->diff(new DateTime($date));
                $age = $interval->y;
                $strDate = $strDate . '(' . $age . self::TEXT_AGE . ')';
                break;
            case self::DATE_WITHOUT_YEAR:
                $strDate = date_format(date_create($date), 'm月 d日');
                $weekdayFormatter = new IntlDateFormatter(
                    self::TIME_ZONE,
                    IntlDateFormatter::NONE,
                    IntlDateFormatter::NONE,
                    date_default_timezone_get(),
                    IntlDateFormatter::GREGORIAN,
                    'EEEEE' // weekday in one letter
                );
                $datetime = new DateTime($date);
                $weekday =  $weekdayFormatter->format($datetime);

                $strDate = $strDate . ' (' . $weekday . ') ';
                break;
            case self::DATE_WEEK:
                $weekdayFormatter = new IntlDateFormatter(
                    self::TIME_ZONE,
                    IntlDateFormatter::NONE,
                    IntlDateFormatter::NONE,
                    date_default_timezone_get(),
                    IntlDateFormatter::GREGORIAN,
                    'EEEEE' // weekday in one letter
                );
                $datetime = new DateTime($date);
                $strDate = $weekdayFormatter->format($datetime);
                break;
            case self::DATE_WITH_TIME:
                $strDate = date_format(date_create($date), 'Y年m月d日 H時i分');
                break;
            case self::TIME_WITHOUT_DATE:
                $strDate = date_format(date_create($date), 'H時i分');
                break;
            case self::DATE_WITH_WEEK_DAY_SLASH:
                $strDate = date_format(date_create($date), 'Y/m/d');
                $weekdayFormatter = new IntlDateFormatter(
                    self::TIME_ZONE,
                    IntlDateFormatter::NONE,
                    IntlDateFormatter::NONE,
                    date_default_timezone_get(),
                    IntlDateFormatter::GREGORIAN,
                    'EEEEE' // weekday in one letter
                );
                $datetime = new DateTime($date);
                $weekday =  $weekdayFormatter->format($datetime);

                $strDate = $strDate . '（' . $weekday . '）';
                break;
            case self::TIME_WITHOUT_DATE_MARK:
                $strDate = date_format(date_create($date), 'H:i');
                break;
            default:
                $strDate = date_format(date_create($date), 'Y年m月d日');
        }

        return $strDate;
    }

    public function converToTimeJPFormat($datetime, $format = "Y-m-d H:i:s")
    {
        $checkDateFormat = DateTime::createFromFormat($format, $datetime);
        if (!$checkDateFormat) {
            return null;
        }

        $convertedDatetime = Carbon::createFromFormat($format, $datetime);
        $aryConvertedDatetime = $convertedDatetime->toArray();
        $convertedTime = '';
        $convertedTime .= !empty($aryConvertedDatetime['hour']) ? $convertedDatetime->format('H') . '時' : '';
        $convertedTime .= !empty($aryConvertedDatetime['minute']) ? $convertedDatetime->format('i') . '分' : '';
        $convertedTime .= !empty($aryConvertedDatetime['second']) ? $convertedDatetime->format('s') . '秒' : '';

        return $convertedTime;
    }

    public function converToDateJPFormat($datetime, $format = "Y-m-d")
    {
        $checkDateFormat = DateTime::createFromFormat($format, $datetime);
        if (!$checkDateFormat) {
            return null;
        }

        $convertedDatetime = Carbon::createFromFormat($format, $datetime);
        $aryConvertedDatetime = $convertedDatetime->toArray();
        $convertedDate = '';
        $convertedDate .= !empty($aryConvertedDatetime['year']) ? $convertedDatetime->format('Y') . '年' : '';
        $convertedDate .= !empty($aryConvertedDatetime['month']) ? $convertedDatetime->format('m') . '月' : '';
        $convertedDate .= !empty($aryConvertedDatetime['day']) ? $convertedDatetime->format('d') . '日' : '';

        return $convertedDate;
    }

    public function converToTimeJPFormatWithoutSecond($datetime, $format = "Y-m-d H:i:s")
    {
        $checkDateFormat = DateTime::createFromFormat($format, $datetime);
        if (!$checkDateFormat) {
            return null;
        }

        $convertedDatetime = Carbon::createFromFormat($format, $datetime);
        $aryConvertedDatetime = $convertedDatetime->toArray();
        $convertedTime = '';
        $convertedTime .= !empty($aryConvertedDatetime['hour']) ? $aryConvertedDatetime['hour'] . ' 時' : '';
        $convertedTime .= !empty($aryConvertedDatetime['minute']) ? $aryConvertedDatetime['minute'] . ' 分' : '';
        return $convertedTime;
    }

    public function diffTime($from, $to = null)
    {
        $checkDateFormat = DateTime::createFromFormat("Y-m-d H:i:s", $to);
        if ($to && !$checkDateFormat) {
            return null;
        }

        $from = Carbon::parse($from);

        if ($to) {
            $to = Carbon::createFromFormat('Y-m-d H:i:s', $to);
        } else {
            $now = Carbon::now();
            $aryNow = $now->toArray();
            if ((0 <= $aryNow['hour'] && $aryNow['minute'] > 0) && $aryNow['hour']  < 2) {
                $date = $now->subDay();
            } else {
                $date = $now;
            }
            $fromTodayString = $date->format('Y-m-d').' 02:00:00';
            $toNextDayString = $date->addDay()->format('Y-m-d').' 01:59:59';

            $fromToday = Carbon::parse($fromTodayString);
            if ($from->between($fromToday, Carbon::now())) {
                $to = Carbon::now();
            } elseif ($from->lessThan($fromToday)) {
                $dateFromTime = $fromToday->format('Y-m-d');
                $to = Carbon::createFromFormat('Y-m-d H:i:s', $dateFromTime.' 01:59:59');
            } else {
                return '---------';
            }
        }

        $diffInSecond = $to->diff($from);
        $diffTime = '';
        $diffTime .= !empty($diffInSecond->h) ? $diffInSecond->h. ' 時間' : '';
        $diffTime .= !empty($diffInSecond->i) ? $diffInSecond->i. ' 分' : '';
        $diffTime .= !empty($diffInSecond->s) ? $diffInSecond->s. ' 秒' : '';
        return $diffTime;
    }

    public function diffTimeInSecond($from, $to = null)
    {
        $checkDateFormat = DateTime::createFromFormat("Y-m-d H:i:s", $to);
        if ($to && !$checkDateFormat) {
            return null;
        }
        $from = Carbon::parse($from);
        if ($to) {
            $to = Carbon::createFromFormat('Y-m-d H:i:s', $to);
        } else {
            $now = Carbon::now();
            $aryNow = $now->toArray();
            if ((0 <= $aryNow['hour'] && $aryNow['minute'] > 0) && $aryNow['hour']  < 2) {
                $date = $now->subDay();
            } else {
                $date = $now;
            }
            $fromToday =  Carbon::now()->format('Y-m-d').' 02:00:00';
            $toNextDay = $date->addDay()->format('Y-m-d').' 01:59:59';
            
            $fromToday = Carbon::parse($fromToday);
            $toNextDay = Carbon::parse($toNextDay);
            if ($from->between($fromToday, Carbon::now())) {
                $to = Carbon::now();
            } elseif ($from->lessThan($fromToday)) {
                // $dateFromTime = $fromToday->format('Y-m-d');
                $to = Carbon::parse(date('Y-m-d', strtotime($from. ' + 1 days')).' 01:59:59');
            } else {
                return 0;
            }
        }

        $diffTime = $to->diffInSeconds($from);

        return $diffTime;
    }

    public function convertToTimeString($second, $type = 1)
    {
        $timeInterval = CarbonInterval::seconds($second);
        $timeInterval = $timeInterval->cascade()->getNonZeroValues();
        $diffTime = '';
        $timeInDay = '';
        if (isset($timeInterval['days'])) {
            $hour = isset($timeInterval['hours']) ? $timeInterval['hours'] : 0;
            $timeInDay = $timeInterval['days']*24 + $hour;
        }
        if (!isset($timeInterval['days']) && isset($timeInterval['hours'])) {
            $timeInDay = isset($timeInterval['hours']) ? $timeInterval['hours'] : '';
        }
        if (self::DATE_WIT_RANGE_TIME == $type) {
            $diffTime .= !empty($timeInDay) ? $timeInDay. '時間' : '';
        } else {
            $diffTime .= !empty($timeInDay) ? $timeInDay. '時' : '';
        }
        $diffTime .= isset($timeInterval['minutes']) ? sprintf('%02d', $timeInterval['minutes']). '分' : '';
        $diffTime .= isset($timeInterval['seconds']) ? sprintf('%02d', $timeInterval['seconds']). '秒' : '';

        return $diffTime;
    }

    public function convertToTimeStringWithoutSecond($second, $type = 1)
    {
        $timeInterval = CarbonInterval::seconds($second);
        $timeInterval = $timeInterval->cascade()->getNonZeroValues();
        $diffTime = '';
        $timeInDay = '';
        if (isset($timeInterval['days'])) {
            $hour = isset($timeInterval['hours']) ? $timeInterval['hours'] : 0;
            $timeInDay = $timeInterval['days']*24 + $hour;
        }
        if (!isset($timeInterval['days']) && isset($timeInterval['hours'])) {
            $timeInDay = isset($timeInterval['hours']) ? $timeInterval['hours'] : '';
        }
        if (self::DATE_WIT_RANGE_TIME == $type) {
            $diffTime .= !empty($timeInDay) ? $timeInDay. '時間' : '';
        } else {
            $diffTime .= !empty($timeInDay) ? $timeInDay. '時' : '';
        }
        $diffTime .= isset($timeInterval['minutes']) ? sprintf('%02d', $timeInterval['minutes']). ' 分' : '';
        $diffTime .= isset($timeInterval['seconds']) ? sprintf('%02d', $timeInterval['seconds']). '秒' : '';

        return $diffTime;
    }

    public function convertDateFormat($date, $dateFormat = "Y-m-d H:i:s")
    {
        if (!$date) {
            return null;
        }
        return date($dateFormat, strtotime($date));
    }

    public function convertDateForChat($date)
    {
        if (!$date) {
            return null;
        }

        if ($date->isToday()) {
            return date('H:i', strtotime($date));
        } else {
            return $date->locale('ja')->calendar();
        }
    }

    public function convertDateStringForChat($date)
    {
        if (!$date) {
            return null;
        }
        $date = Carbon::parse($date);
        
        if (!$date) {
            return null;
        }
        
        if ($date->isToday()) {
            return date('H:i', strtotime($date));
        } else {
            return $date->locale('ja')->calendar();
        }
    }

    public function getFullDatetime($date, $hour, $min = '00')
    {
        return date('Y-m-d', strtotime($date)) . ' ' . $hour . ':' . $min;
    }
}
