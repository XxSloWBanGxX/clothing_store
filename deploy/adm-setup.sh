#!/bin/bash
# Запускати на сервері ADM.tools через SSH (з кореня проєкту Laravel)
set -e

echo "==> ClothStore deploy setup"

if [ ! -f artisan ]; then
    echo "Помилка: запусти скрипт у корені Laravel (де є файл artisan)"
    exit 1
fi

if [ ! -f .env ]; then
    if [ -f deploy/env.production.example ]; then
        cp deploy/env.production.example .env
        echo "Створено .env з deploy/env.production.example — відредагуй DB_* та APP_URL"
    else
        cp .env.example .env
        echo "Створено .env — відредагуй DB_* та APP_URL"
    fi
    php artisan key:generate --force
    echo "Зупинись і заповни .env (nano .env), потім запусти скрипт знову"
    exit 0
fi

echo "==> Composer"
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Права"
chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

echo "==> Storage link"
php artisan storage:link 2>/dev/null || true

echo "==> Міграції"
php artisan migrate --force

echo "==> Кеш production"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Готово"
php artisan about --only=environment,cache,drivers 2>/dev/null || php artisan --version
echo ""
echo "Перевір:"
echo "  1. Document root домену → .../public"
echo "  2. PHP 8.2+ у панелі ADM"
echo "  3. Cron: * * * * * cd $(pwd) && php artisan schedule:run >> /dev/null 2>&1"
