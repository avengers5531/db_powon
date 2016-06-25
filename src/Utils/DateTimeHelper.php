<?php

namespace Powon\Utils;

/**
 * Class DateTimeHelper
 * A utility class with static methods
 * to handle date and time operations.
 * There is no need to handle complicated timezone issues for this project.
 * @package Powon\Utils
 */
class DateTimeHelper
{
    /**
     * Checks if an input has the right format: YYYY-MM-DD
     * The database understands this format.
     * @param $input string
     * @return bool
     */
    public static function validateDateFormat($input) {
        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$input))
        {
            return true;
        } else {
            return false;
        }
    }

}