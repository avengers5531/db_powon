<?php

namespace Powon\Test\unit;


use Powon\Utils\DateTimeHelper;

class DateTimeHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testValidateDateFormat() {
        $this->assertTrue(DateTimeHelper::validateDateFormat('2016-12-19'));
        $this->assertTrue(DateTimeHelper::validateDateFormat('2016-02-29'));
        $this->assertFalse(DateTimeHelper::validateDateFormat('1999-13-11'));
        $this->assertFalse(DateTimeHelper::validateDateFormat('1812-06-32'));
        $this->assertFalse(DateTimeHelper::validateDateFormat('1998-7-3'));
        $this->assertFalse(DateTimeHelper::validateDateFormat('random string'));
        $this->assertFalse(DateTimeHelper::validateDateFormat(12341235124));
        $this->assertTrue(DateTimeHelper::validateDateFormat('1949-03-31'));
    }
}
