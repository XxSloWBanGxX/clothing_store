<?php
/**
 * Fix missing APP_KEY without Laravel bootstrap (upload to www/fix-key.php).
 * Open: http://www.clothstored.shop/fix-key.php?token=clothstore-fix-2026
 * DELETE this file immediately after success.
 */
declare(strict_types=1);

if (($_GET['token'] ?? '') !== 'clothstore-fix-2026') {
    http_response_code(403);
    exit('Forbidden');
}

$envPath = dirname(__DIR__) . '/clothstore-upload/.env';

if (! is_file($envPath)) {
    http_response_code(500);
    exit('.env not found: ' . $envPath . "\nCreate it from deploy/env.production.example first.");
}

$content = file_get_contents($envPath);
if ($content === false) {
    exit('Cannot read .env');
}

if (preg_match('/^APP_KEY=base64:.+/m', $content)) {
    exit("APP_KEY already set in .env\nDelete fix-key.php and open the site.");
}

$key = 'base64:' . base64_encode(random_bytes(32));

if (preg_match('/^APP_KEY=.*/m', $content)) {
    $content = preg_replace('/^APP_KEY=.*/m', 'APP_KEY=' . $key, $content);
} else {
    $content = "APP_KEY={$key}\n" . $content;
}

if (file_put_contents($envPath, $content) === false) {
    http_response_code(500);
    exit('Cannot write .env — fix permissions in ADM (Restore access rights).');
}

header('Content-Type: text/plain; charset=utf-8');
echo "OK: APP_KEY written to .env\n\n";
echo "DELETE www/fix-key.php NOW\n";
echo "Then open: http://www.clothstored.shop\n";
