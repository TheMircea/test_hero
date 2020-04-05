<?php

namespace app\helpers;

class Helper
{

    public static function generateRandomString($len = 10)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($len / strlen($x)))), 1, $len);
    }
}
