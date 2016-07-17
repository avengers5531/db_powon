<?php

namespace Powon\Test\unit;


use Powon\Utils\Validation;

class ValidationTest extends \PHPUnit_Framework_TestCase
{
    public function testValidateParametersExist() {
        $names = ['name1', 'name2', 'name3', 4];

        // not a map
        $res = Validation::validateParametersExist(['name1'], $names);
        $this->assertFalse($res);

        // edge cases
        $res = Validation::validateParametersExist($names, null);
        $this->assertFalse($res);

        $res = Validation::validateParametersExist($names, []);
        $this->assertFalse($res);

        $input = [
            'name1' => 'a',
            'name2' => 'b',
            'name3' => 'c',
            4 => 'd'
        ];
        $res = Validation::validateParametersExist(null, $input);
        $this->assertFalse($res);

        $res = Validation::validateParametersExist([], $input);
        $this->assertFalse($res);

        // normal case
        $res = Validation::validateParametersExist($names, $input);
        $this->assertTrue($res);

        // implicit conversion in named parameter from int to string.
        $input = [
            'name1' => 'a',
            'name2' => 'b',
            'name3' => 'c',
            '4' => 'd'
        ];
        $res = Validation::validateParametersExist($names, $input);
        $this->assertTrue($res);

        // empty field name1
        $input = [
            'name1' => '',
            'name2' => 'b',
            'name3' => 'c',
            4 => 'd'
        ];
        $res = Validation::validateParametersExist($names, $input);
        $this->assertFalse($res);

        // missing field name3
        $input = [
            'name1' => '',
            'name2' => 'b',
            4 => 'd'
        ];
        $res = Validation::validateParametersExist($names, $input);
        $this->assertFalse($res);
    }
}
