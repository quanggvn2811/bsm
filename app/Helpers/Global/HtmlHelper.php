<?php

use App\Helpers\General\HtmlHelper;

if (! function_exists('style')) {
    /**
     * @param       $url
     * @param array $attributes
     * @param null  $secure
     *
     * @return mixed
     */
    function style($url, $attributes = [], $secure = null)
    {
        return resolve(HtmlHelper::class)->style($url, $attributes, $secure);
    }
}

if (! function_exists('script')) {
    /**
     * @param       $url
     * @param array $attributes
     * @param null  $secure
     *
     * @return mixed
     */
    function script($url, $attributes = [], $secure = null)
    {
        return resolve(HtmlHelper::class)->script($url, $attributes, $secure);
    }
}

if (! function_exists('form_cancel')) {
    /**
     * @param        $cancel_to
     * @param        $title
     * @param string $classes
     *
     * @return mixed
     */
    function form_cancel($cancel_to, $title, $classes = 'btn btn-danger btn-sm')
    {
        return resolve(HtmlHelper::class)->formCancel($cancel_to, $title, $classes);
    }
}

if (! function_exists('checkedValueToggle')) {
    /**
     * @param $request value from submit
     * @param $savedValue saved value
     * @param $value checking value
     * @return mixed
     */
    function checkedValueToggle($request, $savedValue, $value, $default = false, $hasError = false)
    {
        // dd($request, $value);
        if ($request || $hasError) {
            if ($value == $request || (is_array($request) && in_array($value, $request))) {
                return true;
            }

            return false;
        }

        if ($savedValue) {
            if ($value == $savedValue || (is_array($savedValue) && in_array($value, $savedValue))) {
                return true;
            }
            return false;
        }

        return $default;
    }
}

if (! function_exists('checkedValueSelectbox')) {
    /**
     * @param $request value from submit
     * @param $savedValue saved value
     * @param array $values selectbox value
     * @return mixed
     */
    function checkedValueSelectbox($request, $savedValue, $values)
    {
        if ($request) {
            $arySelected = array_filter($values, function ($value) use ($request) {
                return in_array($value, $request);
            });
            return array_shift($arySelected);
        }
        if ($savedValue) {
            $arySelected = array_filter($values, function ($value) use ($savedValue) {
                return in_array($value, $savedValue);
            });
            return array_shift($arySelected);
        }
        return '';
    }

    function checkedValueSelectboxDefault($request, $savedValue)
    {
        if ($request) {
            return $request;
        }
        return $savedValue;
    }
}

if (! function_exists('form_submit')) {
    /**
     * @param        $title
     * @param string $classes
     *
     * @return mixed
     */
    function form_submit($title, $classes = 'btn btn-success btn-sm pull-right')
    {
        return resolve(HtmlHelper::class)->formSubmit($title, $classes);
    }
}

if (! function_exists('active_class')) {
    /**
     * Get the active class if the condition is not falsy.
     *
     * @param        $condition
     * @param string $activeClass
     * @param string $inactiveClass
     *
     * @return string
     */
    function active_class($condition, $activeClass = 'active', $inactiveClass = '')
    {
        return $condition ? $activeClass : $inactiveClass;
    }
}

if (! function_exists('addAnchorTag')) {
    /**
     * Add anchors tag to link data
     * 
     * @param $data
     * @return mixed
     */
    function addAnchorTag($data) {
        $pattern = '/^(http|https):\/\/.+/m';
        if (preg_match_all($pattern, $data, $links)) {
            array_map(function ($link) use (&$data) {
                $link = trim($link);
                $tag = '<a href="' . $link . '" target="_blank">' . $link . '</a>';
                $data = str_replace($link, $tag, $data);
            }, $links[0]);
        }

        return $data;
    }
}
