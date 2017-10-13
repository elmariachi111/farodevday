<?php

namespace FaroBundle\Classes;

use Carbon\Carbon;

/**
 * Class for getting german holidays for a given year.
 */
class GermanHolidays {

    /**
     * Get holidays in Berlin for given month and year.
     *
     * @param $month
     * @param $year
     * @return Carbon[]
     */
    public static function getBerlinHolidaysForMonth($month, $year) {
        $dates = self::getBerlinHolidays($year);
        return array_values(array_filter(
            $dates,
            function (Carbon $date) use ($month) {
                return $date->month == $month;
            }
        ));
    }

    /**
     * Get holidays in Berlin for given year.
     *
     * @param int $year Year
     *
     * @return Carbon[]
     */
    public static function getBerlinHolidays($year)
    {
        $easter = self::getEaster($year);

        $dates = [
            Carbon::create($year, 1, 1, 0, 0, 0),   //neujahr
            $easter->copy()->subDays(2),          //karfreitag
            $easter,                              //ostersonntag
            $easter->copy()->addDay(1),           //ostermontag
            Carbon::create($year, 5, 1, 0, 0, 0),   //tag der arbeit
            $easter->copy()->addDay(39),          //palm1tag
            $easter->copy()->addDay(50),          //palm2tag
            Carbon::create($year, 10, 3, 0, 0, 0),  //deutschlands geburtstag
            Carbon::create($year, 12, 25, 0, 0, 0), //jesus geburtstag
            Carbon::create($year, 12, 26, 0, 0, 0), //jesus geburtstag 2
        ];

        if ($year == 2017) { //reformationsgeburtstag
            $dates[] = Carbon::create($year, 10, 31, 0, 0, 0);
        }

        usort(
            $dates,
            function (Carbon $a, Carbon $b) {
                return $a->getTimestamp() <=> $b->getTimestamp();
            }
        );

        return $dates;
    }

    /**
     * Provides a Date object that represents easter sunday for this year.
     *
     * @param int $year the year for which to calculate the easter sunday date.
     *
     * @return Carbon
     */
    public static function getEaster($year)
    {
        $easterSunday = Carbon::create($year, 3, 21)->startOfDay()->addDays(\easter_days($year));
        return $easterSunday;
    }

    public static function isBankHoliday(Carbon $date) {

        $holidays = self::getBerlinHolidays($date->year);

        foreach ($holidays as $holiday) {
            /** @var Carbon $holiday */
            if ($holiday->day == $date->day && $holiday->month == $date->month)
                return true;
        }
        return false;
    }
}
