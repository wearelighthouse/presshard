<?php

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

define('PRESS_HARD', dirname(__DIR__));

if (!defined('THEME')) {
    define('THEME', get_stylesheet_directory());
}
