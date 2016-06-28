<?php

namespace Powon\Utils;

/**
 * Class Token
 * A simple class to generate a token using the SHA-256 algorithm.
 * @package Powon\Utils
 */
class Token {
    
    /**
     * Generates a pseudo-random token based on a key.
     * @param string $key A 
     * @return string the token.
     */
    public static function generate($key) {
        $bytes = openssl_random_pseudo_bytes(4);
        $hex = bin2hex($bytes);
        $token = hash('sha256', $hex. ($key ?: '').time());
        return $token;
    }
}
