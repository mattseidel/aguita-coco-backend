<?php

namespace backend\helpers;

class Helpers
{
    public static function validateDate(string $date)
    {
        $dateEx = explode('-', $date);
        try {
            if ($dateEx[0] !== '') {
                return checkdate($dateEx[1], $dateEx[2], $dateEx[0]);
            }
        }catch (\Exception $e){
            return false;
        }
        return false;
    }
}