<?php

use Carbon\Carbon;

if(! function_exists('local_date')){
    function local_date($date){
        $local_date = (new Carbon($date))->subHour(3)->format('d.m.y H:i:s');

        return $local_date;
    }
}
