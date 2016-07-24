<?php

if (!function_exists('flash')) {
    /**
     * Global accessor for flash messaging system.
     *
     * @param string $msg
     * @return mixed
     */
    function flash($msg = null)
    {
        $flash = app('Olorin\Mgmt\Flash');

        if($msg === null) return $flash;

        return $flash->message($msg);
    }
}

if(!function_exists('camelCase')){
    /**
     * Convert a given string to camelCase by stripping out non-alpha and non-numeric characters,
     * then uppercasing each word's first letter, except the first word.  Include an array of
     * characters in the 2nd parameter that you would like for it to not strip out.
     *
     * @param string $value
     * @param array $noStrip
     * @return mixed
     */
    function camelCase($value, $noStrip = array()) {
        // non-alpha and non-numeric characters become spaces
        $value = preg_replace('/[^a-z0-9' . implode("", $noStrip) . ']+/i', ' ', $value);
        $value = trim($value);

        // uppercase the first character of each word
        $value = ucwords($value);
        $value = str_replace(" ", "", $value);
        $value = lcfirst($value);

        return $value;
    }
}

if(!function_exists('sluggify')){
    /**
     * Prepare a given string to become a URL by stripping unwanted characters
     * and sanitizing the string.
     *
     * @param $url
     * @return string
     */
    function sluggify($url) {
        // Prep string with some basic normalization
        $url = strtolower($url);
        $url = strip_tags($url);
        $url = stripslashes($url);
        $url = html_entity_decode($url);

        // Remove single-quotes/apostrophes (can't, etc.)
        $url = str_replace('\'', '', $url);

        // Replace non-alpha numeric with hyphens
        $url = preg_replace('/[^a-z0-9]+/', '-', $url);
        $url = trim($url, '-');

        return $url;
    }
}