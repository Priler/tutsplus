<?php

class TimeManager {

    public function __construct() {
        //empty one
    }

    static public function time($shift=0) {
        return (time() + $shift);
    }

    static public function isLeapYear($year=false) {
        if (!$year) {
            $year = date('Y');
        }
        return ((bool) ( cal_days_in_month(CAL_GREGORIAN, 2, $year) - 28 ));
    }

    static public function yearDays($year = false) {
        if (!$year) {
            $year = date('Y');
        }
        if (TimeManager::isLeapYear($year)) {
            return 366;
        } else {
            return 365;
        }
    }

    static public function humanizeTime($scs){
            $seconds = $scs;
            $result = $seconds.lng::read('seconds');
            if ($seconds > 60) {
                $minutes = floor( $seconds / 60 );
                $seconds = (int)$seconds % 60;
                $result = $minutes.lng::read('minutes').' '.$seconds.lng::read('seconds');
                if ($minutes > 60) {
                    $hours = floor( $minutes / 60 );
                    $minutes = $minutes % 60;
                    $result = $hours.lng::read('hours').' '.$minutes.lng::read('minutes').' '.$seconds.lng::read('seconds');
                    if ($hours > 24) {
                        $days = floor( $hours / 24 );
                        $hours = $hours % 24;
                        $result = $days.lng::read('days').' '.$hours.lng::read('hours').' '.$minutes.lng::read('minutes').' '.$seconds.lng::read('seconds');
                        if ($days > TimeManager::yearDays()) {
                            $years = floor( $days / TimeManager::yearDays() );
                            $days = $days % TimeManager::yearDays();
                            $result = $years.lng::read('years').' '.$days.lng::read('days').' '.$hours.lng::read('hours').' '.$minutes.lng::read('minutes').' '.$seconds.lng::read('seconds');
                            if ($years > 100) {
                                $years = floor( $years / 100 );
                                $centuries = $years % 100;
                                $result = $centuries.lng::read('centuries').' '.$years.lng::read('years').' '.$days.lng::read('days').' '.$hours.lng::read('hours').' '.$minutes.lng::read('minutes').' '.$seconds.lng::read('seconds');
                            }
                        }
                    }
                }
            }
            return $result;
    }

    static public function humanizeMonth($month) {
        switch($month) {
            case '1':return 'Январь';break;
            case '2':return 'Февраль';break;
            case '3':return 'Март';break;
            case '4':return 'Апрель';break;
            case '5':return 'Май';break;
            case '6':return 'Июнь';break;
            case '7':return 'Июль';break;
            case '8':return 'Август';break;
            case '9':return 'Сентябрь';break;
            case '10':return 'Октябрь';break;
            case '11':return 'Ноябрь';break;
            case '12':return 'Декабрь';break;
        }
    }

    static public function timePlus($format) {
        if ($format == 'now') {
            return TimeManager::time();
        }
        preg_match('#^([0-9]+)#i',$format,$fmatches);
        preg_match('#([A-z]{3,})$#i',$format,$tmatches);
        $count = $fmatches['1'];
        $type = substr($tmatches['1'],0,3);
        if (empty($count) || empty($type)) {
            ExceptionManager::ThrowError(__CLASS__.':'.__FUNCTION__.' - Invalid params.');
        }
        $result = 0;
        switch($type) {
            case'sec';
                $result = TimeManager::time() + $count;
                break;
            case'min';
                $result = TimeManager::time() + ($count * 60);
                break;
            case'hou';
                $result = TimeManager::time() + ($count * 3600);
                break;
            case'day';
                $result = TimeManager::time() + ($count * 86400);
                break;
            case'mon';
                $result = TimeManager::time() + ($count * (86400 * 30));
                break;
            case'yea';
                $result = TimeManager::time() + ($count * (2592000 * 365));
                break;
            case'cen';
                $result = TimeManager::time() + ($count * (946080000 * 100));
                break;
        }
        return $result;
    }

    static public function timeMinus($format) {
        if ($format == 'now') {
            return TimeManager::time();
        }
        preg_match('#^([0-9]+)#i',$format,$fmatches);
        preg_match('#([A-z]{3,})$#i',$format,$tmatches);
        $count = $fmatches['1'];
        $type = substr($tmatches['1'],0,3);
        if (empty($count) || empty($type)) {
            ExceptionManager::ThrowError(__CLASS__.':'.__FUNCTION__.' - Invalid params.');
        }
        $result = 0;
        switch($type) {
            case'sec';
                $result = TimeManager::time() - $count;
                break;
            case'min';
                $result = TimeManager::time() - ($count * 60);
                break;
            case'hou';
                $result = TimeManager::time() - ($count * 3600);
                break;
            case'day';
                $result = TimeManager::time() - ($count * 86400);
                break;
            case'mon';
                $result = TimeManager::time() - ($count * (86400 * 30));
                break;
            case'yea';
                $result = TimeManager::time() - ($count * (2592000 * 365));
                break;
            case'cen';
                $result = TimeManager::time() - ($count * (946080000 * 100));
                break;
        }
        return $result;
    }

    static public function drop_timestamp($timestamp) {
        return strtotime(date('d.m.Y',$timestamp));
    }

    static public function convert_time($timestamp) {
      return date('d.m.Y', $timestamp);
    }

}