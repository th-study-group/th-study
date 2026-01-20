<?php

if (! function_exists('env_default')) {
    function env_default($key, $default = null)
    {
        $value = env($key);

        if ($value === null) {
            return $default;
        }

        if (is_string($value) && trim($value) === '') {
            return $default;
        }

        return $value;
    }
}
