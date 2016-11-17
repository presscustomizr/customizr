<?php
//Helper class to build a simple date diff object
//Alternative to date_diff for php version < 5.3.0
//http://stackoverflow.com/questions/9373718/php-5-3-date-diff-equivalent-for-php-5-2-on-own-function
if ( ! class_exists( 'CZR_DateInterval' ) ) :
  Class CZR_DateInterval {
      /* Properties */
      public $y = 0;
      public $m = 0;
      public $d = 0;
      public $h = 0;
      public $i = 0;
      public $s = 0;

      /* Methods */
      public function __construct ( $time_to_convert ) {
        $FULL_YEAR = 60*60*24*365.25;
        $FULL_MONTH = 60*60*24*(365.25/12);
        $FULL_DAY = 60*60*24;
        $FULL_HOUR = 60*60;
        $FULL_MINUTE = 60;
        $FULL_SECOND = 1;

        //$time_to_convert = 176559;
        $seconds = 0;
        $minutes = 0;
        $hours = 0;
        $days = 0;
        $months = 0;
        $years = 0;

        while($time_to_convert >= $FULL_YEAR) {
            $years ++;
            $time_to_convert = $time_to_convert - $FULL_YEAR;
        }

        while($time_to_convert >= $FULL_MONTH) {
            $months ++;
            $time_to_convert = $time_to_convert - $FULL_MONTH;
        }

        while($time_to_convert >= $FULL_DAY) {
            $days ++;
            $time_to_convert = $time_to_convert - $FULL_DAY;
        }

        while($time_to_convert >= $FULL_HOUR) {
            $hours++;
            $time_to_convert = $time_to_convert - $FULL_HOUR;
        }

        while($time_to_convert >= $FULL_MINUTE) {
            $minutes++;
            $time_to_convert = $time_to_convert - $FULL_MINUTE;
        }

        $seconds = $time_to_convert; // remaining seconds
        $this->y = $years;
        $this->m = $months;
        $this->d = $days;
        $this->h = $hours;
        $this->i = $minutes;
        $this->s = $seconds;
        $this->days = ( 0 == $years ) ? $days : ( $years * 365 + $months * 30 + $days );
      }
  }
endif;