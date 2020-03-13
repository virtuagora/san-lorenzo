<?php

namespace App\Util;

class Utils
{
    static public function randomStr($length, $keyspace = '123456789ABCDEFGHJKLMNPQRSTUVWXYZ')
    {
        $str = '';
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[random_int(0, $max)];
        }
        return $str;
    }

    static public function arrayWhiteList($array, $list) {
        return array_intersect_key($array, array_flip($list));
    }

    // static public function checkBefore($date) {
    //     $deadline = Carbon::parse($date);
    //     $today = Carbon::now();
    //     if ($today->gt($deadline)) {
    //         throw new AppException('Application period is over');
    //     }
    // }
}