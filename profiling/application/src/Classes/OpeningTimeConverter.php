<?php

namespace App\Classes;

use App\Classes\OpeningTime;

class OpeningTimeConverter
{
    /**
     * Convert a string with opening times into OpeningHours objects
     * 
     * @param string $text
     * @return OpeningHours
     */
    public function convert($text)
    {
        
        $text = $this->normalize($text);
        $rawMatches = $this->match($text);
        $mapped = $this->map($rawMatches);
        $optimized = $this->optimize($mapped);
        
        return $optimized;
    }
    
    public function normalize($text)
    {
        // quick & dirty
        
        $month = array(
            'january' => 'jan',
            'february' => 'feb',
            'march' => 'mar',
            'april' => 'apr',
            'may' => 'may',
            'june' => 'jun',
            'july' => 'jul',
            'august' => 'aug',
            'september' => 'sep',
            'october' => 'oct',
            'november' => 'nov',
            'december' => 'dec',
        );
        
        $days = array(
            'mondays' => 'mo',
            'monday' => 'mo',
            'tuesdays' => 'tu',
            'tuesday' => 'tu',
            'wednesdays' => 'we',
            'wednesday' => 'we',
            'thursdays' => 'th',
            'thursday' => 'th',
            'fridays' => 'fr',
            'friday' => 'fr',
            'saturdays' => 'sa',
            'saturday' => 'sa',
            'sundays' => 'su',
            'sunday' => 'su',
        );
   
        $special = array(
            'to' => '-',
            'and' => '+',
            '&' => '+',
            'workdays' => 'mo-fr',
            'weekdays:' => 'mo-fr',
        );
             
        $allDays = 'mo|tu|we|th|fr|sa|su';  // need for regex, no implode from $days!
        
        $norm = strtolower($text);
        
        $replace = array_merge($month, $days, $special);  // replace all days, month, specials
        $norm = str_replace(array_keys($replace), $replace, $norm);

        $norm = str_replace('&nbsp;', ' ', $norm);

        $norm = preg_replace('((\d{1,2})h(\d{2}))', '${1}:${2}', $norm); // 8h30 => 8:30
        $norm = preg_replace('((\d{1,2})h)', '${1}', $norm); // 19h => 19

        $norm = preg_replace('((\d{1,2})[.](\d{2}))', '${1}:${2}', $norm); // 00.00 => 00:00
        $norm = preg_replace('=[^\d](\d{1})[.:](\d{2})=U', ' 0${1}:${2}', $norm); // 0:00 => 00:00, do not remove whitespace
        $norm = preg_replace('((\d{2}):(\d{2})\s+-\s+(\d{2}):(\d{2}))', '${1}:${2}-${3}:${4}', $norm); // 10:30 - 11:30 => 10:30-11:30
        $norm = preg_replace('((\d{2}):(\d{2}))', '${1}:${2}', $norm); // 10:30 - 11:30 => 10:30-11:30
        $norm = preg_replace('((' . $allDays . ')[.:]+)', '${1}', $norm); // fr: => fr
        $norm = preg_replace('(' . $allDays . ')', ' ${0} ', $norm);
        $norm = preg_replace('((' . $allDays . ')\s+-\s+(' . $allDays .'))', '${1}-${2}', $norm); //

        $norm = str_replace(', ', '', $norm); // 

        $norm = preg_replace('=\s+[/]\s+=', ' ', $norm); // 
        $norm = str_replace('&nbsp;', ' ', $norm);
        $norm = str_replace(' - ', '-', $norm); // 
        $norm = trim(preg_replace('/\s+/', ' ', $norm)); // 
        
        return $norm;
    }
    
    /**
     * 
     * @param string $text
     * @return array
     */
    public function match($text)
    {
        // trans
        $d2i = array('mo'=>1,'tu'=>2,'we'=>3,'th'=>4,'fr'=>5,'sa'=>6,'su'=>7);
        $i2d = array_flip($d2i);
        
        $days = 'mo|tu|we|th|fr|sa|su';
        $pattern = '=(?P<startday>' . $days .')(?:-(?P<endday>' . $days .'))?(?:\+(?P<adddays>[a-z+]+))?\s+(?P<starthour>\d{1,2})(?::(?P<startminute>\d{2}))?-(?P<endhour>\d{1,2})(?::(?P<endminute>\d{2}))?=';

        $ms = array();
    
    
        $i=1;
        while(preg_match($pattern, $text, $m)) {
            if($i++ == 10) { break; }
            $text = str_replace($m[0], '', $text);
            $ms[] = $m;
        }

//        print_r($ms);

        // format & group
        $group = array();
    
        foreach($ms as $i=>&$m) {
            if(0 == count($m)) {
                unset($ms[$i]);
                continue;
            }

            foreach(array('starthour', 'startminute', 'endhour', 'endminute') as $key) {
                $m[$key] = isset($m[$key]) && !empty($m[$key]) ? $m[$key] : '00';

                if(strlen($m[$key]) == 1) {
                    $m[$key] = '0' . $m[$key];
                }
            }

            if(!isset($m['endday']) || empty($m['endday'])) {
                $m['endday'] = $m['startday'];
            }

            $m['adddays'] = explode('+', $m['adddays']);

            $m['starttime'] = $m['starthour'] . ':' . $m['startminute'];
            $m['endtime']   = $m['endhour'] . ':' . $m['endminute'];

            // days
            $sdi = $d2i[ $m['startday'] ];
            $edi = $d2i[ $m['endday'] ];
            $m['days'] = range($sdi, $edi);

            foreach($m['adddays'] as $day) {
                if(!empty($day)) {
                    $m['days'][] = $d2i[ $day ];
                }
            }

            sort($m['days']);

            $m['months'] = range(1, 12);
            
            $m['timesig']   = $m['starttime'] . '-' . $m['endtime'];
            $m['daysig']   = implode('-', $m['days']);
            $m['sig'] = $m['daysig'] .'|'. $m['timesig'];
            
            $group[$m['timesig']][] = $m;
        }
        
//        print_r($group);

        
        return $group;
    }
    
    /**
     * Map raw data to objects
     * 
     * @param array $rawdata
     * @return array
     */
    public function map(array $rawdata)
    {
        $mapped = array();
        
        foreach($rawdata as $timesig => $rawItems) {
            foreach($rawItems as $raw) {
                $openingTime = new OpeningTime();

                if($raw['endtime'] > 24){
                    $raw['endtime'] = 24;
                }
                $openingTime->setMonth($raw['months'])
                            ->setDay($raw['days'])
                            ->setTimeRange($raw['starttime'], $raw['endtime']);

                $mapped[$timesig][] = $openingTime;
            }
        }
        
        
        return $mapped;
    }
    
    /**
     * Optimize objects
     * 
     * @param array $mapped
     * @return OpeningHours
     */
    public function optimize(array $mapped)
    {
        $optimized = array();
        
        foreach($mapped as $timesig => $openingTimes) {

            $tmpOpeningTime = new OpeningTime();
            
            foreach($openingTimes as $openingTime) {
                $tmpOpeningTime->merge($openingTime);
            }
            
//            echo '#'.PHP_EOL . $tmpOpeningTime;
            $optimized[] = $tmpOpeningTime;
        }
        

//        foreach($optimized as $ot) {
//            echo $ot . PHP_EOL;
//        }

        return new OpeningHours($optimized);
    }
}
