<?php
/**
 * Here is the serverless function entry
 * for deployment with Vercel.
 */

// Vercel only allows write access to /tmp
$tmpStorage = '/tmp/storage';
$dirs = [
    $tmpStorage,
    $tmpStorage . '/framework',
    $tmpStorage . '/framework/views',
    $tmpStorage . '/framework/cache',
    $tmpStorage . '/framework/sessions',
    $tmpStorage . '/logs',
    $tmpStorage . '/app',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// Forward serverless environment variables if needed
putenv("VIEW_COMPILED_PATH={$tmpStorage}/framework/views");
putenv("SESSION_DRIVER=" . (getenv('SESSION_DRIVER') ?: 'cookie'));
putenv("CACHE_STORE=" . (getenv('CACHE_STORE') ?: 'array'));
putenv("LOG_CHANNEL=" . (getenv('LOG_CHANNEL') ?: 'stderr'));

require __DIR__.'/../public/index.php';