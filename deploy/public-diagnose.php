<?php
/**
 * Diagnose Laravel 500 on ADM hosting. Upload to www/diagnose.php
 * http://www.clothstored.shop/diagnose.php?token=clothstore-diag-2026
 * DELETE after use.
 */
declare(strict_types=1);

if (($_GET['token'] ?? '') !== 'clothstore-diag-2026') {
    http_response_code(403);
    exit('Forbidden');
}

header('Content-Type: text/plain; charset=utf-8');

$root = dirname(__DIR__) . '/clothstore-upload';
$envPath = $root . '/.env';
$logPath = $root . '/storage/logs/laravel.log';

echo "=== ClothStore diagnose ===\n\n";

if (! is_file($root . '/artisan')) {
    exit("FAIL: Laravel root not found: {$root}\n");
}

echo "[OK] Laravel root exists\n";

if (! is_file($envPath)) {
    exit("FAIL: .env missing\n");
}

echo "[OK] .env exists\n";

$vars = [];
foreach (file($envPath, FILE_IGNORE_NEW_LINES) as $line) {
    $line = trim($line);
    if ($line === '' || str_starts_with($line, '#') || ! str_contains($line, '=')) {
        continue;
    }
    [$k, $v] = explode('=', $line, 2);
    $vars[trim($k)] = trim($v, " \t\"'");
}

$key = $vars['APP_KEY'] ?? '';
echo '[APP_KEY] ' . (str_starts_with($key, 'base64:') ? 'OK' : 'MISSING or invalid') . "\n";

$host = $vars['DB_HOST'] ?? '127.0.0.1';
$db = $vars['DB_DATABASE'] ?? '';
$user = $vars['DB_USERNAME'] ?? '';
$pass = $vars['DB_PASSWORD'] ?? '';

echo "[DB] host={$host} db={$db} user={$user}\n";

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$db};charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $tables = ['users', 'products', 'categories', 'sessions', 'migrations'];
    foreach ($tables as $t) {
        try {
            $n = (int) $pdo->query("SELECT COUNT(*) FROM `{$t}`")->fetchColumn();
            echo "[TABLE {$t}] OK ({$n} rows)\n";
        } catch (Throwable $e) {
            echo "[TABLE {$t}] FAIL: {$e->getMessage()}\n";
        }
    }
} catch (Throwable $e) {
    echo '[DB] FAIL: ' . $e->getMessage() . "\n";
}

$dirs = [
    $root . '/storage',
    $root . '/storage/logs',
    $root . '/storage/framework',
    $root . '/storage/framework/cache',
    $root . '/storage/framework/sessions',
    $root . '/storage/framework/views',
    $root . '/bootstrap/cache',
];

foreach ($dirs as $d) {
    $w = is_dir($d) && is_writable($d);
    echo '[WRITE ' . basename(dirname($d)) . '/' . basename($d) . '] ' . ($w ? 'OK' : 'FAIL not writable') . "\n";
}

foreach (glob($root . '/bootstrap/cache/*.php') as $f) {
    @unlink($f);
}
echo "[CACHE] bootstrap/cache cleared\n";

if (is_file($logPath)) {
    $lines = file($logPath, FILE_IGNORE_NEW_LINES);
    $tail = array_slice($lines, -25);
    echo "\n=== Last laravel.log lines ===\n";
    echo implode("\n", $tail) . "\n";
} else {
    echo "\n[LOG] laravel.log not found\n";
}

echo "\nDELETE www/diagnose.php NOW\n";
