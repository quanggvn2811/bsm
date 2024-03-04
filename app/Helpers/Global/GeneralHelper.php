<?php

if (!function_exists('app_name')) {
    /**
     * Helper to grab the application name.
     *
     * @return mixed
     */
    function app_name()
    {
        return config('app.name');
    }
}

if (!function_exists('gravatar')) {
    /**
     * Access the gravatar helper.
     */
    function gravatar()
    {
        return app('gravatar');
    }
}

if (!function_exists('home_route')) {
    /**
     * Return the route to the "home" page depending on authentication/authorization status.
     *
     * @return string
     */
    function home_route()
    {
        if (auth()->check()) {
            if (auth()->user()->can('view backend')) {
                return 'admin.dashboard';
            }

            return 'frontend.user.dashboard';
        }

        return 'frontend.index';
    }
}

if (!function_exists('convert_to_1byte')) {
    /**
     * Return string that only contains 1byte characters
     *
     * @return string
     */
    function convert_to_1byte($data)
    {
        if (!$data) {
            return '';
        }

        $ary2Byte = [
            '０',
            '１',
            '２',
            '３',
            '４',
            '５',
            '６',
            '７',
            '８',
            '９',
        ];

        $ary1Byte = [
            '0',
            '1',
            '2',
            '3',
            '4',
            '5',
            '6',
            '7',
            '8',
            '9',
        ];

        return str_replace($ary2Byte, $ary1Byte, $data);
    }
}

if (!function_exists('mb_substr_replace')) {
    function mb_substr_replace($str, $repl, $start, $length = null)
    {
        preg_match_all('/./us', $str, $ar);
        preg_match_all('/./us', $repl, $rar);
        $length = is_int($length) ? $length : utf8_strlen($str);
        array_splice($ar[0], $start, $length, $rar[0]);
        return implode($ar[0]);
    }
}
