#!/bin/bash
# Post-deploy on ADM (safe cache clear — no config:cache)
set -e
cd ~/clothstored.shop/clothstore-upload

php artisan view:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true

rm -f bootstrap/cache/config.php bootstrap/cache/routes.php bootstrap/cache/services.php 2>/dev/null || true

chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

echo "Deploy OK: $(date -Iseconds)"
