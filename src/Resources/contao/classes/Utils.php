<?php

namespace Kmielke\CalendarExtendedBundle;

class Utils
{
    /**
     * @param $arr the array the value should be appended to a key/the key should be added
     * @param $key the key to look for
     * @param $val the value which should be append/set for the given key
     * @return void
     */
    static public function appendToArrayKey(&$arr, $key, $val) {
        if (array_key_exists($key, $arr)) {
            $arr[$key] .= $val;
        } else {
            $arr[$key] = $val;
        }
    }

}