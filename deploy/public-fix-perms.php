<?php
/**
 * Fix storage permissions on ADM. Upload to www/fix-perms.php
 * http://www.clothstored.shop/fix-perms.php?token=clothstore-perms-2026
 * DELETE after use.
 */
declare(strict_types=1);

if (($_GET['token'] ?? '') !== 'clothstore-perms-2026') {
    http_response_code(403);
    exit('Forbidden');
}

header('Content-Type: text/plain; charset=utf-8');

$root = dirname(__DIR__) . '/clothstore-upload';

$paths = [
    $root . '/storage',
    $root . '/storage/framework',
    $root . '/storage/framework/sessions',
    $root . '/storage/framework/views',
    $root . '/storage/framework/cache',
    $root . '/bootstrap/cache',
];

function fixPath(string $path): array
{
    $results = [];
    if (! is_dir($path)) {
        @mkdir($path, 0775, true);
    }
    @chmod($path, 0775);
    $results[] = $path . ' -> ' . (is_writable($path) ? 'writable' : 'NOT writable');

    if (is_dir($path)) {
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($it as $item) {
            @chmod($item->getPathname(), is_dir($item->getPathname()) ? 0775 : 0664);
        }
    }

    return $results;
}

echo "=== Fix permissions ===\n\n";

foreach ($paths as $p) {
    foreach (fixPath($p) as $line) {
        echo $line . "\n";
    }
}

foreach (glob($root . '/bootstrap/cache/*.php') as $f) {
    @unlink($f);
}
echo "\n[CACHE] cleared\n";

$s = is_writable($root . '/storage/framework/sessions');
$v = is_writable($root . '/storage/framework/views');

echo "\nSessions writable: " . ($s ? 'YES' : 'NO') . "\n";
echo "Views writable: " . ($v ? 'YES' : 'NO') . "\n";

if ($s && $v) {
    echo "\nOK. Open http://www.clothstored.shop\n";
} else {
    echo "\nIf still NO: ADM -> Hosting -> bg622152 -> Restore access rights\n";
}

echo "\nDELETE www/fix-perms.php NOW\n";
