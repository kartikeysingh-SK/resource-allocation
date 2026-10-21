<?php
declare(strict_types=1);

/**
 * Vercel Serverless Front-Controller for RTRAE Engine.
 * Directs clean routes or .php page requests to root PHP files on Vercel.
 */

$rawUri = $_SERVER['REQUEST_URI'] ?? '/';
$path   = parse_url($rawUri, PHP_URL_PATH);
$path   = trim((string)$path, '/');

// Root or index
if ($path === '' || $path === 'index.php') {
    require __DIR__ . '/../index.php';
    exit;
}

// 1. Direct match with exact path
$directPath = realpath(__DIR__ . '/../' . $path);
$rootDir    = realpath(__DIR__ . '/../');

if ($directPath && str_starts_with($directPath, $rootDir) && is_file($directPath)) {
    require $directPath;
    exit;
}

// 2. Direct match appending .php (e.g. /dashboard -> dashboard.php)
$phpPath = realpath(__DIR__ . '/../' . $path . '.php');
if ($phpPath && str_starts_with($phpPath, $rootDir) && is_file($phpPath)) {
    require $phpPath;
    exit;
}

// Fallback 404
http_response_code(404);
header('Content-Type: text/html; charset=utf-8');
echo '<!DOCTYPE html><html><head><title>404 Not Found</title></head><body style="font-family:sans-serif;text-align:center;padding:50px;"><h2>404 - Page Not Found</h2><p><a href="/">Return to RTRAE Home</a></p></body></html>';
