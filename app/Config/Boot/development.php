<?php

// FORCE FIX KINT FOR PHP 8.2+
// if (class_exists(\Kint\Kint::class)) {
//     \Kint\Kint::$file_link_format = '%f:%l';
// }

/*
 |--------------------------------------------------------------------------
 | ERROR DISPLAY
 |--------------------------------------------------------------------------
 | In development, we want to show as many errors as possible to help
 | make sure they don't make it to production. And save us hours of
 | painful debugging.
 */
// error_reporting(-1);
error_reporting(E_ALL);
ini_set('display_errors', 1);

/*
 |--------------------------------------------------------------------------
 | DEBUG BACKTRACES
 |--------------------------------------------------------------------------
 | If true, this constant will tell the error screens to display debug
 | backtraces along with the other error information. If you would
 | prefer to not see this, set this value to false.
 */
defined('SHOW_DEBUG_BACKTRACE') || define('SHOW_DEBUG_BACKTRACE', true);

/*
 |--------------------------------------------------------------------------
 | DEBUG MODE
 |--------------------------------------------------------------------------
 | Debug mode is an experimental flag that can allow changes throughout
 | the system. This will control whether Kint is loaded, and a few other
 | items. It can always be used within your own application too.
 */
defined('CI_DEBUG') || define('CI_DEBUG', true);

// Kint\Renderer\RichRenderer::$theme = 'aante-light.css';
// Kint::$enabled_mode = false;

// if (class_exists(\Kint\Kint::class)) {
//     \Kint\Kint::$file_link_format = '';
// }
