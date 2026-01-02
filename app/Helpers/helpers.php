<?php

if (!function_exists('verta')) {
    /**
     * Create a Verta (Jalali/Persian) datetime instance
     * This is an alias for jdate() from morilog/jalali package
     *
     * @param mixed $datetime
     * @return \Morilog\Jalali\Jalalian
     */
    function verta($datetime = null)
    {
        return jdate($datetime);
    }
}
