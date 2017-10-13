<?php

namespace FaroBundle\Classes;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use FaroBundle\Classes\GermanHolidays;

class Time {

    const ONE_WEEK = 7 * 24 * 60 * 60;
    const ONE_DAY = 24 * 60 * 60;

    /**
     * @var int
     */
    protected $hours;

    /**
     * @var int
     */
    protected $minutes;

    public function __construct($hours, $minutes) {
        $this->hours = $hours;
        $this->minutes=$minutes;
    }

    /**
     * @return int
     */
    public function getHours() {
        return $this->hours;
    }

    /**
     * @param int $hours
     */
    public function setHours($hours) {
        $this->hours = $hours;
    }

    /**
     * @return int
     */
    public function getMinutes() {
        return $this->minutes;
    }

    /**
     * @param int $minutes
     */
    public function setMinutes($minutes) {
        $this->minutes = $minutes;
    }

    public function getMinutesOfDay(){
        return ($this->hours * 60) + $this->minutes;
    }

    /**
     * @param $frac float fracion of day
     * @return Time
     */
    public static function byFraction($frac) {

        if ($frac == 1) {
            return new Time(23,59);
        }
        $hours = $frac * 24;
        $iHours = round($hours, 0, PHP_ROUND_HALF_DOWN);
        $mins = $hours - $iHours;
        $iMins = abs(round($mins * 60, 0, PHP_ROUND_HALF_UP));

        return new Time($iHours,$iMins);

    }

    /**
     * recognizes that a string is a date for various formats,
     * mainly used to detect birthdates
     *
     * @param $string
     * @return bool|Carbon
     */
    public static function recognizeDate($string) {
        try {
            $pattern = '/^\d{6}$/ims';
            if (1 == preg_match($pattern, $string)) {
                $parts = str_split($string, 2);
                if ( ($parts[0] > 31) || ($parts[0] == 0) || ($parts[1] > 12) || ($parts[0] == 0)) {
                    return false;
                }
                if ($parts[2] === '00') {
                    $parts[2] = '2000';
                }
                $d = Carbon::parse("$parts[0].$parts[1].$parts[2]");
                return $d;
            }

            $pattern = '/^\d{8}$/ims';
            if (1 == preg_match($pattern, $string)) {
                $parts = str_split($string, 2);
                if ( ($parts[0] > 31) || ($parts[0] == 0) || ($parts[1] > 12) || ($parts[0] == 0)) {
                    return false;
                }
                $year = substr($string, 4);
                $d = Carbon::parse("$parts[0].$parts[1].$year");
                return $d;
            }

            $pattern = '/(\d+)\.(\d+).(\d+)/ims';
            $matches = [];
            if (1 == preg_match($pattern, $string, $matches)) {
                if ($matches[3] == '00') { //make year 2000 work as expected
                    $dateString = $matches[1] . '.' . $matches[2] . '.2000';
                } else {
                    $dateString = $matches[0];
                }

                $date = Carbon::parse($dateString);
                return $date;
            }
        } catch (\Exception $exception) {}

        return false;
    }

    /**
     * @param Carbon $date
     * @return bool
     */
    public static function isWorkDay(Carbon $date) {
        if ($date->isWeekend()) {
            return false;
        }

        foreach (GermanHolidays::getBerlinHolidaysForMonth($date->month, $date->year) as $holiday) {
            if ($holiday->day == $date->day) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param Carbon $date
     * @param int    $days
     * @param string $op 'add' | 'sub'
     * @return Carbon
     */
    public static function opWorkDays(Carbon $date, int $days, $op = 'add') {
        $res = $date->copy()->$op(1, 'day');
        $dayDiffCounter = Time::isWorkDay($res) ? 1 : 0;
        while ($dayDiffCounter < $days || !Time::isWorkDay($res)) {
            $res->$op(1, 'day');
            $dayDiffCounter = Time::isWorkDay($res) ? $dayDiffCounter + 1 : $dayDiffCounter;
        }

        return $res;
    }
}
