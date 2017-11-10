<?php

if (!function_exists('pr')) {
    /**
     * @return void
     */
    function pr($var)
    {
        echo '<pre>';
            print_r($var);
        echo '</pre>';
    }
}
