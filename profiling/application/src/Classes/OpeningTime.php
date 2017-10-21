<?php

namespace App\Classes;

class OpeningTime
{
    const MONTH_OFFSET = 0;
    const DAY_OFFSET   = 12;
    const TIME_OFFSET  = 19;
    
    const MASK_SIZE = 32;

    /**
     * @var integer
     */
    private $mask1 = 0;
    private $mask2 = 0;
    private $mask3 = 0;
    private $mask4 = 0;
    
    public function __toString()
    {
        $tmp = array();
        for($i = 1; $i <= 4; $i++) {
            $name = 'mask' . $i;
            $tmp[] = 'Mask' . ($i) . ': ' . str_pad(decbin($this->{$name}), self::MASK_SIZE, 0, STR_PAD_LEFT);
        }
        
        return '[' . implode(', ', $tmp) . ']';
    }
    
    /**
     * Create a OpeningTime object from DateTime
     * 
     * @param \DateTime $datetime
     * @return OpeningTime
     */
    public static function createFromDateTime(\DateTime $datetime = null)
    {
        if(null === $datetime) {
            $datetime = new \DateTime();
        }
        
        $openingTime = new static();
        $openingTime->setMonth($datetime->format('n'), true)
                    ->setDay($datetime->format('N'), true)
                    ->setTime($datetime->format('H:i'), true);
        return $openingTime;
    }
    
    /**
     * Set flag(s) at position
     * 
     * @param integer|array $positions
     * @param boolean $value OPTIONAL true
     * @return OpeningTime
     */
    public function setFlag($positions, $value = true)
    {
        foreach((array)$positions as $position) {
            list($mask, $maskPosition) = $this->getIndexByPosition($position);

            if((boolean)$value) {
                $this->{$mask} |= 1 << $maskPosition;
            } else {
                $this->{$mask} -= 1 << $maskPosition;
            }
        }
        
        return $this;
    }
       
    /**
     * 
     * @param integer $position
     * @return boolean
     */
    public function hasFlag($position)
    {
        list($mask, $maskPosition) = $this->getIndexByPosition($position);
        
        $maskCompareTo = 0 | (1 << $maskPosition);
        return ($this->{$mask} & $maskCompareTo) == $maskCompareTo;
    }
        
    /**
     * 
     * @param integer|array $month (january=1, december=12)
     * @param boolean $value OPTIONAL true
     * @return OpeningTime
     */
    public function setMonth($month, $value = true)
    {
        return $this->setFlag($this->shiftOffset($month, self::MONTH_OFFSET - 1), $value);
    }
    
    /**
     * 
     * @param integer $month
     * @return boolean
     */
    public function hasMonth($month)
    {
        return $this->hasFlag($this->shiftOffset($month, self::MONTH_OFFSET - 1));
    }
    
    /**
     * 
     * @return array
     */
    public function getMonthRanges()
    {
        $ranges = array();
        $range = null;
        
        for($month = 1; $month <= 12; $month++) {
            // find start day for range
            if($this->hasMonth($month) && !is_array($range)) {
                $range = array('start' => $month, 'end' => null);
            }
            
            // find end day for range
            if(!$this->hasMonth($month) && is_array($range)) {
                $range['end'] = $month - 1;
                $ranges[] = $range;
                $range = null;
            } else if(12 == $month && is_array($range)) {
                $range['end'] = $month;
                $ranges[] = $range;    
            }
        }
        
        return $ranges;
    }
    
    /**
     * 
     * @param integer|array $day (monday=1, sunday=7)
     * @param boolean $value OPTIONAL true
     * @return OpeningTime
     */
    public function setDay($day, $value = true)
    {
        return $this->setFlag($this->shiftOffset($day, self::DAY_OFFSET - 1), $value);
    }
    
    /**
     * 
     * @param integer $day
     * @return boolean
     */
    public function hasDay($day)
    {
        return $this->hasFlag($this->shiftOffset($day, self::DAY_OFFSET - 1));
    }
    
    /**
     * Retrieve an array with day ranges
     * 
     * array(
     *     array('start' => 1, 'end' => 3) => monday to wednesday
     *     array('start' => 6, 'end' => 7) => saturday and sunday
     *     array('start' => 7, 'end' => 7) => just sunday
     * )
     * 
     * @return array
     */
    public function getDayRanges()
    {
        $ranges = array();
        $range = null;
        
        for($day = 1; $day <= 7; $day++) {
            // find start day for range
            if($this->hasDay($day) && !is_array($range)) {
                $range = array('start' => $day, 'end' => null);
            }
            
            // find end day for range
            if(!$this->hasDay($day) && is_array($range)) {
                $range['end'] = $day - 1;
                $ranges[] = $range;
                $range = null;
            } else if(7 == $day && is_array($range)) {
                $range['end'] = $day;
                $ranges[] = $range;    
            }
        }
        
        return $ranges;
    }
    
    /**
     * 
     * @param string $time
     * @param boolean $value OPTIONAL true
     * @return type
     */
    public function setTime($time, $value = true)
    {
        return $this->setFlag($this->getTimeOffset($time), $value);
    }
    
    /**
     * 
     * @param string $time1
     * @param string $time2
     * @param boolean $value OPTIONAL true
     * @return OpeningTime
     */
    public function setTimeRange($time1, $time2, $value = true)
    {
        if(19 == ($offset2 = $this->getTimeOffset($time2))) {
            $offset2 += 97;
        }

        $range = range($this->getTimeOffset($time1),
                       $offset2);
        
        return $this->setFlag($range, $value);
    }
    
    /**
     * 
     * @return array
     */
    public function getTimeRanges()
    {
        $ranges = array();
        $range = null;
        
        // 96 = 24 hours * 4 quarters
        $maxOffset = self::TIME_OFFSET+97;
        for($i = self::TIME_OFFSET; $i < $maxOffset; $i++) {
            $open = $this->hasFlag($i);
            $time = $this->getTimeByOffset($i);
            
            if($open && !is_array($range)) {
                $range = array('start' => $time, 'end' => null);
            }
            
            if(!$open && is_array($range)) {
                $range['end'] = $lastTime;
                $ranges[] = $range;
                $range = null;
            } else if($i == $maxOffset-1 && is_array($range)) {
                $range['end'] = $time;
                $ranges[] = $range;
            }
            
            $lastTime = $time;
        }
        
        return $ranges;
    }
    
    /**
     * 
     * @param string $time
     * @return boolean
     */
    public function hasTime($time)
    {
        return $this->hasFlag($this->getTimeOffset($time));
    }
    
    /**
     * 
     * @param OpeningTime
     * @return boolean
     */
    public function equals(OpeningTime $openingTime)
    {
        return $this->mask1() === $openingTime->mask1() &&
               $this->mask2() === $openingTime->mask2() &&
               $this->mask3() === $openingTime->mask3() &&
               $this->mask4() === $openingTime->mask4();
    }
    
    /**
     * 
     * @param OpeningTime
     * @return boolean
     */    
    public function match(OpeningTime $openingTime)
    {
        return (($this->mask1 & $openingTime->mask1) == $openingTime->mask1 &&
                ($this->mask2 & $openingTime->mask2) == $openingTime->mask2 &&
                ($this->mask3 & $openingTime->mask3) == $openingTime->mask3 &&
                ($this->mask4 & $openingTime->mask4) == $openingTime->mask4);
    }
    
    /**
     * 
     * @param OpeningTime $openingTime
     * @return OpeningTime
     */
    public function merge(OpeningTime $openingTime)
    {
        $this->mask1 |= $openingTime->mask1;
        $this->mask2 |= $openingTime->mask2;
        $this->mask3 |= $openingTime->mask3;
        $this->mask4 |= $openingTime->mask4;
        return $this;
    }
    
    /**
     * Calculate the bit offset for a time string
     * 
     * @param string $time
     * @return integer
     */
    public function getTimeOffset($time)
    {
        preg_match('=(?P<hour>\d{1,2})(?::(?P<minute>\d{2}))?=', $time, $match);
        if(!isset($match['minute'])) {
            $match['minute'] = 0;
        }
        
        $offset  = $match['hour'] * 4;
        $offset += ($match['minute'] - ($match['minute'] % 15)) / 15;

        return self::TIME_OFFSET + $offset;   
    }
            
    /**
     * Get a time string by offset
     * 
     * @param integer $offset
     * @return string
     */
    public function getTimeByOffset($offset)
    {
        // in case of midnight closing hour...
        if($offset == self::TIME_OFFSET + 96) {
            return '00:00';
        }
        
        $offset -= self::TIME_OFFSET;
        $hours = range(0, 23);
        $minutes = array('00', 15, 30, 45);
        
        $hourOffset = floor($offset / 4);
        $minuteOffset = ($offset % 4);

        return ($hours[$hourOffset] < 10 ? '0' : '') . $hours[$hourOffset] . 
               ':' . $minutes[$minuteOffset];
    }
    
    /**
     * 
     * @param \Datetime $datetime OPTIONAL
     * @return boolean
     */
    public function isOpen(\Datetime $datetime = null)
    {
        return $this->match(self::createFromDateTime($datetime));
    }

    /**
     *
     * @return \DateTime current day with openingTime endDate as time
     */
    public function isOpenUntil(){
        $t = $this->getTimeRanges();
        $dateTime = \DateTime::createFromFormat("H:i",$t[0]['end']);

        // because endtime 00:00 is new day
        if($t[0]['end'] == "00:00"){
            return $dateTime->modify('+1 day');
        } else {
            return $dateTime;
        }
    }

    /**
     * Calculate the bit position
     * 
     * @param integer $position
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function getIndexByPosition($position)
    {
        if($position < 0 || $position > 124) {
            throw new \InvalidArgumentException('Parameter $position must be between 0 - 124 (' . $position . ').');
        }

        return array(
            0 => 'mask' . ceil(($position + 1) / (self::MASK_SIZE - 1)),
            1 => (self::MASK_SIZE - 2) - ($position % (self::MASK_SIZE - 1))
        );
    }
    
    /**
     * 
     * @param integer|array $positions
     * @param integer $shift OPTIONAL -1
     * @return array
     */
    protected function shiftOffset($positions, $shift = -1)
    {
        $tmp = array();
        foreach((array)$positions as $index => $position) {
            $tmp[$index] = $position + $shift;
        }
        return is_array($positions) ? $tmp : $tmp[0];
    }
}
