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

// Symfony's runtime re-requires SCRIPT_FILENAME to get the app closure. For an
// extensionless path the built-in server already points it at index.php, but for
// one like /sitemap.xml it points back here — and requiring this file a second
// time yields int(1) instead of the closure, which the runtime rejects. nginx
// does not have the problem: fastcgi_param sets SCRIPT_FILENAME to index.php.
$_SERVER['SCRIPT_FILENAME'] = $root . '/index.php';

require $root . '/index.php';
