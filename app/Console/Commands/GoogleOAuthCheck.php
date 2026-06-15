<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GoogleOAuthCheck extends Command
{
    protected $signature = 'google:check';

    protected $description = 'Перевірити налаштування Google OAuth';

    public function handle(): int
    {
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');
        $redirect = config('services.google.redirect');

        $this->line('APP_URL: ' . config('app.url'));
        $this->line('Redirect URI: ' . ($redirect ?: '(порожньо)'));
        $this->line('Client ID: ' . ($clientId ? substr($clientId, 0, 12) . '...' : '(порожньо — не налаштовано)'));
        $this->line('Client Secret: ' . ($clientSecret ? 'OK (є)' : '(порожньо — не налаштовано)'));
        $this->line('google.local.php: ' . (is_file(config_path('google.local.php')) ? 'знайдено' : 'немає'));

        if (empty($clientId) || empty($clientSecret)) {
            $this->error('Google OAuth не налаштовано.');
            $this->line('Відредагуй config/google.local.php і встав Client ID + Secret.');
            return self::FAILURE;
        }

        $this->info('Google OAuth налаштовано. Спробуй /auth/google/redirect');

        return self::SUCCESS;
    }
}
