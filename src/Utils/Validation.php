<?php

namespace Powon\Utils;


class Validation
{
    /**
     * Given some parameter names, it checks if those parameters exist and are not empty in the input.
     * @param $names [string] 
     * @param $input [string:string] 
     * @return bool True when validation is successful, false otherwise.
     */
    public static function validateParametersExist($names, $input)
    {
        if (!$names || !$input)
            return false;

        foreach ($names as &$name) {
           if (!isset($input[$name]) || empty($input[$name]))
               return false;
        }
        return true;
    }
}
