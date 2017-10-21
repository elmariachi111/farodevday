<?php

namespace App\Tests;

use Carbon\Carbon;
use App\Classes\OpeningTime;
use App\Classes\OpeningTimeConverter;
use App\Classes\TimeDiff;
use PHPUnit\Framework\TestCase;

class OpeningTimesTest extends TestCase
{
    /**
     * @var OpeningTimeConverter $openingTimeConverter
     */
    private $openingTimeConverter;

    protected function setUp()
    {
        $this->openingTimeConverter = new OpeningTimeConverter();
    }

    public function testOpeningTimesConversion() {

        $opt = $this->openingTimeConverter->convert('mo 8-20, tu 9-12, we 8-20, th 7-17, fr 8-19, sa 10-16, su 12-16');

        $this->assertEquals([['start' => 1, 'end' => 1], ['start' => 3, 'end' => 3]], $opt->getOpeningTimes()[0]->getDayRanges());
        $this->assertEquals([['start' => '08:00', 'end' => '20:00']], $opt->getOpeningTimes()[0]->getTimeRanges());

        $this->assertTrue($opt->isOpen(Carbon::parse('2017-10-13 09:37')));
        $this->assertFalse($opt->isOpen(Carbon::parse('2017-10-14 16:30')));

        //$this->assertTrue($openingHours->isWithinOpeningHours(Carbon::parse('2017-10-13 12:00')));

    }

    public function testComplexOpeningTimesConversion() {
        $s = 'mo-th: 09:00-18:30 fr: 08:30-18:30 sa: 09:00-18:00';
        $opt = $this->openingTimeConverter->convert($s);

        //todo: this is a problem: the parser yields +15 minutes.
        //$this->assertFalse($opt->isOpen(Carbon::parse('2017-10-13 18:35')));

        $this->assertTrue($opt->isOpen(Carbon::parse('2017-10-13 15:40')));
        $this->assertFalse($opt->isOpen(Carbon::parse('2017-10-15 15:40')));
        $this->assertFalse($opt->isOpen(Carbon::parse('2017-10-12 08:40')));
        $this->assertTrue($opt->isOpen(Carbon::parse('2017-10-12 09:00')));
    }

    public function testTimeDiff() {
        $s = 'mo-fr: 09:00-18:00';
        $opt = $this->openingTimeConverter->convert($s);

        $timeDiff = new TimeDiff($opt);

        $from = Carbon::parse('2017-03-01 08:00');
        $to   = Carbon::parse('2017-03-03 19:00');

        $outOfOfficeMinutes = $timeDiff->diffWithoutNonworkingHours($from, $to);
        $this->assertEquals(1875, $outOfOfficeMinutes);
    }
}
