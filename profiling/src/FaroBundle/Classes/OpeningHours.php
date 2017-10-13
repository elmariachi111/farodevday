<?php


namespace FaroBundle\Classes;

use Carbon\Carbon;

class OpeningHours
{
    /**
     * @var OpeningTime[]
     */
    private $openingTimes;

    public function __construct(array $openingTimes)
    {
        $this->openingTimes = $openingTimes;
    }

    /**
     * @return OpeningTime[]
     */
    public function getOpeningTimes(): array
    {
        return $this->openingTimes;
    }

    public function isOpen(\DateTime $date) {

        foreach($this->openingTimes as $openingTime) {
            if ($openingTime->isOpen($date)) {
                return true;
            }
        }
        return false;
    }
}