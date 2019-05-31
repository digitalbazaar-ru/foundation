<?php

use Illuminate\Support\Str;

if ( ! function_exists('env')) {
    /**
     * Gets the value of an environment variable. Supports boolean, empty and null.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function env($key, $default = null)
    {
        $value = getenv($key);
        if ($value === false) return value($default);
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        if (Str::startsWith($value, '"') && Str::endsWith($value, '"')) {
            return substr($value, 1, -1);
        }
        return $value;
    }
}

if ( ! function_exists('setenv')) {
    /**
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    function setenv($key, $value = null)
    {
        (new \Dotenv\Loader(''))->setEnvironmentVariable($key, $value);
    }
}

if ( ! function_exists('arrayKeyCamelMapper')) {
    function arrayKeyCamelMapper($element)
    {

        if (!is_array($element)) {
            return $element;
        }

        $result = [];

        foreach ($element as $key => $value) {

            if (strpos($key, 'PROPERTY_') !== false) {
                $key = substr_replace($key, '', 0, 9);
            }

            $camelCaseKey = Str::camel(strtolower($key));


            $result[$camelCaseKey] = $value;

        }

        return $result;
    }
}

if ( ! function_exists('templateStr')) {
    function templateStr($str, $values = [], $clearDuplicateSpaces = true, $delimiter = '#')
    {
        $matches = [];
        preg_match_all('~' . $delimiter . '(.*?)' . $delimiter . '~s', $str, $matches);

        foreach ($matches[1] as $key => $keyName) {
            $matches[1][$key] = array_get($values, $keyName, '');
        }

        $result = trim(str_replace($matches[0], $matches[1], $str));

        return $clearDuplicateSpaces ? preg_replace('/\s{2,}/', ' ', $result) : $result;
    }
}

if ( ! function_exists('buildHtmlParams')) {
    function buildHtmlParams($array = [])
    {
        return implode(' ', array_map(function ($key) use ($array) {
            if (is_bool($array[$key])) {
                return $array[$key] ? $key : '';
            }
            return $key . '="' . $array[$key] . '"';
        }, array_keys($array)));
    }
}