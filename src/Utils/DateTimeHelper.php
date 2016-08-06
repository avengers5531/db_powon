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
    const mysql_format = 'Y-m-d H:i:s';

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

    /**
     * Returns the current time stamp in a format MySQL understands.
     * YYYY-MM-DD hh:mm:ss
     * @return string
     */
    public static function getCurrentTimeStamp()
    {
       return date(self::mysql_format, time());
    }

    /**
     * @param $datetime string
     * @return \DateTime
     */
    public static function fromString($datetime)
    {
        return new \DateTime($datetime);
    }

    /**
     * Returns a datetime object as a string that mysql understands (for binding variables, etc...)
     * @param \DateTime $dateTime
     * @return string
     */
    public static function toString(\DateTime $dateTime)
    {
        return $dateTime->format(self::mysql_format);
    }

    /**
     * Returns the number of seconds in a date interval.
     * @param \DateInterval $dateInterval
     * @return int seconds
     */
    public static function dateIntervalToSeconds($dateInterval)
    {
        $reference = new \DateTimeImmutable;
        $endTime = $reference->add($dateInterval);

        return $endTime->getTimestamp() - $reference->getTimestamp();
    }

    /**
     * Get date in a format MySQL understands: YYYY-MM-DD
     * @return bool|string
     */
    public static function getCurrentDate() {
        return date('Y-m-d', time());
    }
}
