<?php

use App\Helpers\JalaliHelper;

if (!function_exists('toJalali')) {
    function toJalali($date, $format = 'Y/m/d') {
        return JalaliHelper::toJalali($date, $format);
    }
}

if (!function_exists('toGregorian')) {
    function toGregorian($jalaliDate) {
        return JalaliHelper::toGregorian($jalaliDate);
    }
}
if (!function_exists('jalaliToday')) {
    function jalaliToday() {
        return JalaliHelper::today();  
    }
}