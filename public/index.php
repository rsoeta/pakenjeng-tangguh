<?php

// Check PHP version
$minPhpVersion = '8.1';
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    die("Your PHP version must be {$minPhpVersion} or higher to run CodeIgniter. Current: " . PHP_VERSION);
}

// Path to the front controller
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Set the current directory
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

// Load our paths config file
require FCPATH . '../app/Config/Paths.php';

$paths = new Config\Paths();

// Load the framework bootstrap file
require $paths->systemDirectory . '/Boot.php';

exit(CodeIgniter\Boot::bootWeb($paths));
