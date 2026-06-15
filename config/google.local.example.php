<?php

/**
 * Скопіюй цей файл як google.local.php і встав свої ключі з Google Cloud Console.
 * cp config/google.local.example.php config/google.local.php
 */
return [
    'client_id' => '123456789-xxxx.apps.googleusercontent.com',
    'client_secret' => 'GOCSPX-xxxxxxxxxxxxxxxx',
    'redirect' => 'http://127.0.0.1:8000/auth/google/callback',
];
