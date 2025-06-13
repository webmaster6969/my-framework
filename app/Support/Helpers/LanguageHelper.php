<?php

use Core\Support\Session\Session;

if (!function_exists('language')) {
    /**
     * @param string $default
     * @return string
     */
    function language(string $default = 'en'): string
    {
        if (!empty($_GET['lang'])) {
            Session::set('lang', $_GET['lang']);
        } elseif (!empty(Session::get('lang'))) {
            Session::set('lang', Session::get('lang'));
        } else {
            Session::set('lang', $default);
        }

        $lang = Session::get('lang', $default);
        return is_string($lang) ? $lang : $default;
    }
}