<?php

use Core\Support\Session\Session;
use Core\Translator\Translator;

if (!function_exists('t')) {
    /**
     * @param string $key
     * @param array<string, string> $replace
     * @return string
     */
    function t(string $key, array $replace = []): string
    {
        if (empty(Translator::getInstance())) {
            return $key;
        }

        $lang = Session::get('lang');

        if (empty($lang) || !is_string($lang)) {
            $lang = 'en';
        }


        return Translator::get($key, $replace, $lang);
    }
}