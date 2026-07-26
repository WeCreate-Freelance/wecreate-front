<?php
// Router for PHP's built-in web server (see scripts/serve.ps1).
//
// Without this, `php -S -t src/public` returns 404 for /about-us and /contact,
// because the built-in server does not rewrite unknown paths to the front
// controller the way nginx does via `try_files $uri /index.php`.

$root = __DIR__ . '/src/public';
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Let the server deliver real files (assets/, mail.php) straight from disk.
if ($path !== '/' && is_file($root . $path)) {
    return false;
}

$_SERVER['SCRIPT_NAME'] = '/index.php';

require $root . '/index.php';
