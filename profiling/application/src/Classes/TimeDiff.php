<?php


namespace App\Classes;


use Carbon\Carbon;
use Carbon\CarbonInterval;
use App\Classes\GermanHolidays;

class TimeDiff
{
    /** @var  OpeningHours $openingHours */
    private $openingHours;

    public function __construct($openingHours) {
        $this->openingHours = $openingHours;
    }

    /**
     * @param $date1 Carbon
     * @param $date2 Carbon
     * @return int seconds when not considering out of office hours + weekends
     */
    public function diffWithoutNonworkingHours($date1, $date2) {

        $outOfOfficeFilter = function(Carbon $date) { //in PHP7.1: \Closure::fromCallable([self::class, 'isOutOfOfficeTime']);
            return $this->isOutOfOfficeTime($date);
        };

        $outOfOfficeMinutes = $date1->diffFiltered(CarbonInterval::minutes(), $outOfOfficeFilter, $date2, true);

        return $outOfOfficeMinutes;
    }

    public function isOutOfOfficeTime(Carbon $date) {

        if (GermanHolidays::isBankHoliday($date)) {
            return true;
        }

        $isOpen = $this->openingHours->isOpen($date);
        return !$isOpen;

    }

    public function isInOfficeTime(Carbon $date) {
        return !$this->isOutOfOfficeTime($date);
    }

}