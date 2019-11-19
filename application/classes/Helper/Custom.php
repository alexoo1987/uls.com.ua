<?php defined('SYSPATH') or die('No direct script access.');

class Helper_Custom
{
    public static function getDay ($day)
    {
        switch ($day) {
            case ($day < 1):
                return "1 день";
            case 1:
                return $day." день";
            case ($day > 1 && $day < 5):
                return $day." дня";
            case ($day >= 5):
                return $day." дней";
        }
    }
}
