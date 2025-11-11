<?php

declare(strict_types=1);

/**
 * fix_kint.php v2_final
 *
 * Usage:
 *   php scripts/fix_kint.php
 *
 * Meant to be run as composer post-install/post-update script.
 */

$START = microtime(true);

/* -----------------------
   Simple ANSI helpers
   ----------------------- */
$ANSI = (object)[
    'reset' => "\033[0m",
    'bold'  => "\033[1m",
    'green' => "\033[32m",
    'yellow' => "\033[33m",
    'red'   => "\033[31m",
    'blue'  => "\033[34m",
];

function cli($text, $color = ''): void
{
    global $ANSI;
    $out = ($color ? $color : '') . $text . $ANSI->reset . PHP_EOL;
    echo $out;
}

/* -----------------------
   Project root & log
   ----------------------- */
$scriptDir = realpath(__DIR__) ?: __DIR__;
$projectRoot = realpath($scriptDir . '/..') ?: $scriptDir . '/..';
$logDir = $projectRoot . DIRECTORY_SEPARATOR . 'writable' . DIRECTORY_SEPARATOR . 'logs';
$logFile = $logDir . DIRECTORY_SEPARATOR . 'fix_env.log';

function ensureLogDir(string $dir): void
{
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

ensureLogDir($logDir);

function logMsg(string $level, string $message): void
{
    global $logFile;
    $time = date('Y-m-d H:i:s');
    $line = "[$time] [$level] $message" . PHP_EOL;
    @file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
}

/* -----------------------
   Environment detection
   ----------------------- */
function detectEnvironment(string $root): string
{
    // Heuristics:
    // - Windows/Laragon: C:\laragon exists or PHP_OS contains WIN
    // - Hosting: linux (most likely)
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        return 'laragon-windows';
    }
    if (is_dir('C:\\laragon')) {
        return 'laragon-windows';
    }
    // common hosting path hint
    if (strpos($root, '/home/') === 0 || strpos($root, '/var/www') === 0) {
        return 'linux-hosting';
    }
    return 'unknown';
}

$env = detectEnvironment($projectRoot);
cli("🔧 Menjalankan auto-patch CodeIgniter environment…", $ANSI->bold);
cli("Detected environment: $env", $ANSI->blue);
logMsg('INFO', "Script started. Environment: $env");

/* -----------------------
   Helper: backup + safe write
   ----------------------- */
function backupFile(string $file): ?string
{
    if (!file_exists($file)) {
        return null;
    }
    $bak = $file . '.bak.' . date('YmdHis');
    if (@copy($file, $bak)) {
        return $bak;
    }
    return null;
}

function safePutFile(string $file, string $content): bool
{
    $dir = dirname($file);
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
    $tmp = $file . '.tmp.' . uniqid();
    if (@file_put_contents($tmp, $content, LOCK_EX) === false) {
        return false;
    }
    if (!@rename($tmp, $file)) {
        @unlink($tmp);
        return false;
    }
    return true;
}

/* -----------------------
   Patch functions
   ----------------------- */

function patch_kint_getIdeLink(string $kintFile): bool
{
    // Find and replace the getIdeLink method with a safer implementation.
    if (!is_file($kintFile)) {
        return false;
    }
    $src = file_get_contents($kintFile);
    if ($src === false) return false;

    // If safe implementation already exists, skip.
    if (strpos($src, 'static::$file_link_format ??') !== false || strpos($src, 'getIdeLink(string $file, int $line)') === false) {
        return false;
    }

    $pattern = '/public\s+static\s+function\s+getIdeLink\([^\{]*\)\s*\{.*?\n\s*\}/s';
    $replacement = <<<PHP
public static function getIdeLink(string \$file, int \$line): string
{
    // Safe fallback: ensure file_link_format is a string before replacing.
    \$format = static::\$file_link_format ?? '%f:%l';
    if (!is_string(\$format) || \$format === '') {
        return \$file . ':' . \$line;
    }
    // Use str_replace (not preg) — safe and fast.
    return str_replace(['%f', '%l'], [\$file, \$line], \$format);
}
PHP;

    if (preg_match($pattern, $src)) {
        $bak = backupFile($kintFile);
        $new = preg_replace($pattern, $replacement, $src, 1);
        if ($new === null) {
            return false;
        }
        if (safePutFile($kintFile, $new)) {
            logMsg('INFO', "Patched getIdeLink() in $kintFile (backup: " . ($bak ?? 'none') . ')');
            return true;
        }
    }

    return false;
}

function patch_abstractrenderer_sortProperties(string $file): bool
{
    if (!is_file($file)) {
        return false;
    }
    $src = file_get_contents($file);
    if ($src === false) return false;

    // Detect if safe merge already present
    if (strpos($src, "foreach (\$containers as \$c)") !== false && strpos($src, 'array_merge($merged') !== false) {
        return false;
    }

    // Target the call_user_func_array line; replace with safe iterative merge
    $pattern = "/return\s+\\\\call_user_func_array\(\s*'array_merge'\s*,\s*\\\$containers\s*\)\s*;/";
    if (!preg_match($pattern, $src)) {
        // maybe the exact pattern differs; try a looser pattern:
        $pattern = "/return\s+\\\\call_user_func_array\(\s*'array_merge'\s*,\s*\\\$containers\s*\)\s*;/s";
    }

    if (preg_match($pattern, $src)) {
        $bak = backupFile($file);
        $replacement = <<<'PHP'
$merged = [];
foreach ($containers as $c) {
    // ensure each bucket is an array before merging
    if (is_array($c) && !empty($c)) {
        $merged = array_merge($merged, $c);
    }
}
return $merged;
PHP;
        $new = preg_replace($pattern, $replacement, $src, 1);
        if ($new === null) return false;
        if (safePutFile($file, $new)) {
            logMsg('INFO', "Patched AbstractRenderer::sortProperties() in $file (backup: " . ($bak ?? 'none') . ')');
            return true;
        }
    }
    return false;
}

/* -----------------------
   .htaccess and .user.ini templates
   ----------------------- */

function ensure_htaccess(string $target): bool
{
    // Minimal safe .htaccess for CI4 + PHP 8.1 (Laragon / Hosting)
    $content = <<<HT
<IfModule mod_rewrite.c>
    RewriteEngine On
    # Ensure index.php is hidden (CI4 front controller)
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php/$1 [L,NC,QSA]
</IfModule>

# Force PHP 8.1 handler (if host supports alt php handlers)
<IfModule mime_module>
    AddHandler application/x-httpd-alt-php81 .php .php7 .phtml
</IfModule>

# Security: disable directory listing
Options -Indexes

HT;
    return safePutFile($target, $content);
}

function ensure_user_ini(string $target): bool
{
    // Enforce PHP 8.1-friendly values (you can adapt these)
    $content = <<<INI
; .user.ini for PHP 8.1 enforcement
memory_limit = 512M
upload_max_filesize = 2048M
post_max_size = 2048M
max_execution_time = 0
max_input_time = -1
session.gc_maxlifetime = 36000
display_errors = Off

INI;
    return safePutFile($target, $content);
}

/* -----------------------
   Validate writable/ folders
   ----------------------- */
function validateWritable(string $root): array
{
    $paths = [
        $root . DIRECTORY_SEPARATOR . 'writable',
        $root . DIRECTORY_SEPARATOR . 'writable' . DIRECTORY_SEPARATOR . 'cache',
        $root . DIRECTORY_SEPARATOR . 'writable' . DIRECTORY_SEPARATOR . 'logs',
        $root . DIRECTORY_SEPARATOR . 'writable' . DIRECTORY_SEPARATOR . 'session',
        $root . DIRECTORY_SEPARATOR . 'writable' . DIRECTORY_SEPARATOR . 'uploads',
    ];

    $results = [];
    foreach ($paths as $p) {
        if (!file_exists($p)) {
            @mkdir($p, 0777, true);
        }
        $writable = is_writable($p);
        $results[$p] = $writable;
        if (!$writable) {
            // Try to chmod (best-effort)
            @chmod($p, 0777);
            $results[$p] = is_writable($p);
        }
    }
    return $results;
}

/* -----------------------
   Main: discover vendor files
   ----------------------- */

$vendorPath = $projectRoot . DIRECTORY_SEPARATOR . 'vendor';
$kintPath = $vendorPath . DIRECTORY_SEPARATOR . 'kint-php' . DIRECTORY_SEPARATOR . 'kint' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Kint.php';
$abstractRenderer = $vendorPath . DIRECTORY_SEPARATOR . 'kint-php' . DIRECTORY_SEPARATOR . 'kint' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Renderer' . DIRECTORY_SEPARATOR . 'AbstractRenderer.php';

// Also cover older paths (kint v5 can be under vendor/kint-php/kint)
if (!file_exists($kintPath)) {
    $kintPathAlt = $vendorPath . DIRECTORY_SEPARATOR . 'kint-php' . DIRECTORY_SEPARATOR . 'kint' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Kint.php';
    if (file_exists($kintPathAlt)) $kintPath = $kintPathAlt;
}

$patched = 0;

/* Patch getIdeLink */
try {
    if (file_exists($kintPath)) {
        $ok = patch_kint_getIdeLink($kintPath);
        if ($ok) {
            cli("✅ Patch Kint::getIdeLink() (safe syntax) diterapkan.", $ANSI->green);
            logMsg('INFO', "Applied getIdeLink patch to $kintPath");
            $patched++;
        } else {
            cli("ℹ️  Patch Kint::getIdeLink() sudah ada atau tidak diperlukan.", $ANSI->yellow);
            logMsg('INFO', "getIdeLink patch not needed for $kintPath");
        }
    } else {
        cli("⚠️  Kint file not found at expected path: $kintPath", $ANSI->yellow);
        logMsg('WARN', "Kint file not found: $kintPath");
    }
} catch (\Throwable $e) {
    cli("❌ Gagal mem-patch Kint::getIdeLink(): " . $e->getMessage(), $ANSI->red);
    logMsg('ERROR', "getIdeLink patch error: " . $e->getMessage());
}

/* Patch AbstractRenderer::sortProperties */
try {
    if (file_exists($abstractRenderer)) {
        $ok = patch_abstractrenderer_sortProperties($abstractRenderer);
        if ($ok) {
            cli("✅ Patch AbstractRenderer::sortProperties() diterapkan (safe array_merge).", $ANSI->green);
            logMsg('INFO', "Applied sortProperties patch to $abstractRenderer");
            $patched++;
        } else {
            cli("ℹ️  Patch AbstractRenderer::sortProperties() sudah ada atau tidak diperlukan.", $ANSI->yellow);
            logMsg('INFO', "sortProperties patch not needed for $abstractRenderer");
        }
    } else {
        cli("⚠️  AbstractRenderer not found at expected path: $abstractRenderer", $ANSI->yellow);
        logMsg('WARN', "AbstractRenderer not found: $abstractRenderer");
    }
} catch (\Throwable $e) {
    cli("❌ Gagal mem-patch AbstractRenderer::sortProperties(): " . $e->getMessage(), $ANSI->red);
    logMsg('ERROR', "sortProperties patch error: " . $e->getMessage());
}

/* -----------------------
   Ensure .htaccess & .user.ini in public (and root)
   ----------------------- */
$publicHtaccess = $projectRoot . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . '.htaccess';
$rootHtaccess = $projectRoot . DIRECTORY_SEPARATOR . '.htaccess'; // some hosts use root
$userIni = $projectRoot . DIRECTORY_SEPARATOR . '.user.ini';

try {
    if (ensure_htaccess($publicHtaccess)) {
        cli("✅ File public/.htaccess diperbarui untuk PHP 8.1 dan rewrite CI4.", $ANSI->green);
        logMsg('INFO', 'Updated public/.htaccess');
    } else {
        cli("⚠️  Gagal memperbarui public/.htaccess (permissions?).", $ANSI->yellow);
        logMsg('WARN', 'Failed to update public/.htaccess');
    }

    if (!file_exists($rootHtaccess)) {
        // create a simple redirect to public if the app is hosted at project root
        $rootContent = <<<HT
# Redirect requests to the public folder (if using shared hosting)
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_URI} !^/public/
    RewriteRule ^(.*)$ public/\$1 [L]
</IfModule>

HT;
        safePutFile($rootHtaccess, $rootContent);
    }

    if (ensure_user_ini($userIni)) {
        cli("✅ File .user.ini diperbarui untuk PHP 8.1.", $ANSI->green);
        logMsg('INFO', 'Updated .user.ini');
    } else {
        cli("⚠️  Gagal memperbarui .user.ini.", $ANSI->yellow);
        logMsg('WARN', 'Failed to update .user.ini');
    }
} catch (\Throwable $e) {
    cli("❌ Error saat menulis .htaccess / .user.ini: " . $e->getMessage(), $ANSI->red);
    logMsg('ERROR', 'htaccess/user.ini error: ' . $e->getMessage());
}

/* -----------------------
   Validate writable folders
   ----------------------- */
cli("🔎 Validasi hak akses folder writable/* ...", $ANSI->blue);
$results = validateWritable($projectRoot);
foreach ($results as $p => $ok) {
    if ($ok) {
        cli(" - OK: $p", $ANSI->green);
    } else {
        cli(" - ❌ Not writable: $p (please fix manually)", $ANSI->red);
    }
    logMsg('INFO', ($ok ? 'writable' : 'not-writable') . " $p");
}

/* -----------------------
   Summary & exit
   ----------------------- */
$elapsed = round(microtime(true) - $START, 2);
cli("", '');
cli($ANSI->bold . "🎉 Semua patch lingkungan selesai! ($elapsed sec)" . $ANSI->reset, $ANSI->green);
logMsg('INFO', "Script finished in {$elapsed}s. Patches applied: $patched");

exit(0);
