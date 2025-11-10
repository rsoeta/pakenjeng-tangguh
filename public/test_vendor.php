<?php
require __DIR__ . '/../vendor/autoload.php';

echo "<pre>";
echo "✅ PHP version: " . PHP_VERSION . "\n";
echo "✅ CodeIgniter loaded: " . (class_exists('CodeIgniter\\CodeIgniter') ? "Yes" : "No") . "\n";
echo "✅ Intl extension: " . (extension_loaded('intl') ? "Yes" : "No") . "\n";
echo "</pre>";
