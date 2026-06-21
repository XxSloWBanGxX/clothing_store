<?php
/**
 * One-time Laravel setup for shared hosting without SSH.
 * 1. Upload to www/ as setup-once.php
 * 2. Open https://clothstored.shop/setup-once.php?token=CHANGE_ME
 * 3. Delete this file immediately after "OK"
 */
declare(strict_types=1);

$token = 'clothstore-setup-2026';
if (($_GET['token'] ?? '') !== $token) {
    http_response_code(403);
    exit('Forbidden');
}

$laravelRoot = dirname(__DIR__) . '/clothstore-upload';

if (! is_file($laravelRoot . '/artisan')) {
    http_response_code(500);
    exit('Laravel root not found: ' . $laravelRoot);
}

require $laravelRoot . '/vendor/autoload.php';
$app = require_once $laravelRoot . '/bootstrap/app.php';

/** @var \Illuminate\Contracts\Console\Kernel $kernel */
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

$out = [];

if (empty(env('APP_KEY'))) {
    $kernel->call('key:generate', ['--force' => true]);
    $out[] = 'APP_KEY generated';
} else {
    $out[] = 'APP_KEY already set';
}

@chmod($laravelRoot . '/storage', 0775);
@chmod($laravelRoot . '/bootstrap/cache', 0775);

try {
    $kernel->call('migrate', ['--force' => true]);
    $out[] = 'migrate: OK';
} catch (Throwable $e) {
    $out[] = 'migrate: ' . $e->getMessage();
}

try {
    $kernel->call('config:cache');
    $out[] = 'config:cache OK';
} catch (Throwable $e) {
    $out[] = 'config:cache: ' . $e->getMessage();
}

try {
    $kernel->call('route:cache');
    $out[] = 'route:cache OK';
} catch (Throwable $e) {
    $out[] = 'route:cache: ' . $e->getMessage();
}

try {
    $kernel->call('view:cache');
    $out[] = 'view:cache OK';
} catch (Throwable $e) {
    $out[] = 'view:cache: ' . $e->getMessage();
}

header('Content-Type: text/plain; charset=utf-8');
echo "ClothStore setup finished\n\n";
echo implode("\n", $out);
echo "\n\nDELETE www/setup-once.php NOW!\n";
