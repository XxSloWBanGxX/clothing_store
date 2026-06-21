<?php
/**
 * Test MySQL credentials from .env (upload to www/db-test.php).
 * Open: http://www.clothstored.shop/db-test.php?token=clothstore-db-2026
 * DELETE immediately after use.
 */
declare(strict_types=1);

if (($_GET['token'] ?? '') !== 'clothstore-db-2026') {
    http_response_code(403);
    exit('Forbidden');
}

$envPath = dirname(__DIR__) . '/clothstore-upload/.env';
if (! is_file($envPath)) {
    exit('.env not found');
}

$vars = [];
foreach (file($envPath, FILE_IGNORE_NEW_LINES) as $line) {
    $line = trim($line);
    if ($line === '' || str_starts_with($line, '#') || ! str_contains($line, '=')) {
        continue;
    }
    [$k, $v] = explode('=', $line, 2);
    $vars[trim($k)] = trim($v, " \t\"'");
}

$host = $vars['DB_HOST'] ?? 'localhost';
$db = $vars['DB_DATABASE'] ?? '';
$user = $vars['DB_USERNAME'] ?? '';
$pass = $vars['DB_PASSWORD'] ?? '';

header('Content-Type: text/plain; charset=utf-8');

echo "DB_HOST={$host}\n";
echo "DB_DATABASE={$db}\n";
echo "DB_USERNAME={$user}\n";
echo "DB_PASSWORD=" . (strlen($pass) ? '(set, ' . strlen($pass) . ' chars)' : '(EMPTY!)') . "\n\n";

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$db};charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $count = (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
    echo "OK: Connected to database.\n";
    echo "Products in DB: {$count}\n";
    echo "\nDELETE www/db-test.php NOW\n";
} catch (Throwable $e) {
    echo "FAIL: " . $e->getMessage() . "\n\n";
    echo "Fix DB password in ADM -> MySQL -> Databases -> Change password\n";
    echo "Then update clothstore-upload/.env (DB_PASSWORD=...)\n";
}
