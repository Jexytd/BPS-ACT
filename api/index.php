<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

try {
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    error_log('LARAVEL ERROR: ' . $e->getMessage());
    error_log('FILE: ' . $e->getFile());
    error_log('LINE: ' . $e->getLine());
    error_log($e->getTraceAsString());

    http_response_code(500);

    echo '<pre>';
    echo 'ERROR: ' . htmlspecialchars($e->getMessage()) . "\n";
    echo 'FILE: ' . htmlspecialchars($e->getFile()) . "\n";
    echo 'LINE: ' . $e->getLine() . "\n";
    echo '</pre>';
}