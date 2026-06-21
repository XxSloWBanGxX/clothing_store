# Деплой ClothStore на ADM.tools / Ukraine.com.ua

## Що потрібно купити

1. **Хостинг** (тариф з **SSH** і **PHP 8.2+**, від ~150–250 грн/міс)
2. **Домен** (або підключити свій)

---

## Крок 1 — Панель ADM

1. Увійди на [adm.tools](https://adm.tools) / [ukraine.com.ua](https://ukraine.com.ua)
2. **Сайти → Додати сайт** — прив’язати домен
3. **PHP** — версія **8.2** або **8.3**
4. **MySQL → Бази даних** — створити БД, користувача, пароль (запиши!)
5. **SSH** — увімкни / створи доступ (логін, пароль або ключ)
6. **Коренева папка сайту (Document root)** — має вказувати на **`public`**:
   - Якщо проєкт лежить у `/home/USER/domains/site.com/clothstore/`
   - Document root: `/home/USER/domains/site.com/clothstore/public`

---

## Крок 2 — Завантажити файли

### Варіант A — через SSH + Git (зручніше)

```bash
ssh USER@SERVER
cd ~/domains/ТВІЙ-ДОМЕН.com
git clone ТВОЙ_РЕПОЗИТОРИЙ clothstore
cd clothstore
```

### Варіант B — ZIP (FTP / Файловий менеджер)

На **Windows** у PowerShell з папки проєкту:

```powershell
.\deploy\prepare-upload.ps1
```

З’явиться `clothstore-upload.zip` — завантаж на сервер і розпакуй у папку сайту.

**Не завантажуй** `.env` з локального комп’ютера (там root без пароля).

На сервері потрібен **`vendor/`** — або:
- залий разом із zip (якщо робив `composer install --no-dev` локально), **або**
- на сервері: `composer install --no-dev`

---

## Крок 3 — База даних

### Якщо переносиш з XAMPP (є товари, замовлення)

На **локальному** ПК:

```powershell
C:\xampp\mysql\bin\mysqldump.exe -u root clothing_store > deploy\clothing_store.sql
```

У **phpMyAdmin** на ADM: імпорт `clothing_store.sql` у нову базу.

### Якщо база порожня

На сервері після `.env`:

```bash
php artisan migrate --force
```

---

## Крок 4 — Налаштування на сервері (SSH)

```bash
cd ~/domains/ТВІЙ-ДОМЕН.com/clothstore
cp deploy/env.production.example .env
nano .env   # DB_*, APP_URL, APP_DEBUG=false
bash deploy/adm-setup.sh
```

Або вручну:

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
chmod -R ug+rwx storage bootstrap/cache
```

---

## Крок 5 — Фото товарів

Переконайся, що на сервері є папка:

`public/assets/images/products/`

(усі `.jpg` з локального XAMPP). Без неї товари будуть без картинок.

---

## Крок 6 — SSL

ADM → **SSL → Let's Encrypt** для домену (безкоштовно).

Після SSL у `.env`:

```
APP_URL=https://твій-домен.com
```

Потім: `php artisan config:cache`

---

## Крок 7 — Cron (знижки, розсилка, сповіщення)

ADM → **Планувальник (Cron)**:

```
* * * * * cd /home/USER/domains/ТВІЙ-ДОМЕН.com/clothstore && php artisan schedule:run >> /dev/null 2>&1
```

Опційно раз на годину:

```
0 * * * * cd /шлях/до/clothstore && php artisan shop:send-alerts >> /dev/null 2>&1
```

---

## Крок 8 — Перевірка

- Головна, каталог, картка товару, фото
- `/sale`, кошик, checkout
- `/admin` — вхід адміна
- HTTPS без попереджень

---

## Типові проблеми

| Проблема | Рішення |
|----------|---------|
| 500 помилка | `storage/logs/laravel.log`, права на `storage/` |
| Сторінки крім головної 404 | Document root → `public`, `.htaccess` у public |
| Немає стилів | `APP_URL` з https, `php artisan config:cache` |
| БД не підключається | `DB_HOST=localhost`, перевір ім’я/пароль у панелі |
| Імпорт ZIP >40MB | `php artisan shop:import-products ...` через SSH |

---

## Що надіслати, якщо деплоїмо разом

- Домен
- SSH: `host`, `login` (пароль — краще в особистому / не в чат)
- Дані MySQL з панелі ADM
- Чи переносимо локальну БД чи чистий старт
