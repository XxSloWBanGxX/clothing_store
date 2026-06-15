<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect()
    {
        if (empty(config('services.google.client_id'))) {
            return redirect('/login')->withErrors([
                'general' => 'Google не налаштовано. Встав Client ID у config/google.local.php або GOOGLE_CLIENT_ID у .env, потім php artisan config:clear',
            ]);
        }

        return Socialite::driver('google')
            ->redirectUrl(config('services.google.redirect'))
            ->redirect();
    }

    public function callback()
    {
        if (empty(config('services.google.client_id'))) {
            return redirect('/login')->withErrors([
                'general' => 'Вхід через Google ще не налаштовано.',
            ]);
        }

        try {
            $socialUser = Socialite::driver('google')
                ->redirectUrl(config('services.google.redirect'))
                ->user();
        } catch (\Throwable $e) {
            return redirect('/login')->withErrors([
                'general' => 'Не вдалося увійти через Google. Спробуй ще раз.',
            ]);
        }

        $email = $socialUser->getEmail();

        if (empty($email)) {
            return redirect('/login')->withErrors([
                'general' => 'Google-акаунт не надав email. Спробуй інший спосіб входу.',
            ]);
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            $baseUsername = Str::slug(Str::before($email, '@'), '_');
            if ($baseUsername === '') {
                $baseUsername = 'user';
            }

            $username = $baseUsername;
            $i = 1;
            while (User::where('username', $username)->exists()) {
                $username = $baseUsername . '_' . $i++;
            }

            $user = User::create([
                'name' => $socialUser->getName() ?: ($socialUser->getNickname() ?: 'Користувач'),
                'username' => $username,
                'email' => $email,
                'phone' => $this->placeholderPhone($email),
                'password' => Hash::make(Str::random(32)),
                'role' => 'user',
                'is_verified' => true,
            ]);
        }

        Auth::login($user);

        if ($user->role === 'admin') {
            return redirect('/admin');
        }

        return redirect('/');
    }

    private function placeholderPhone(string $email): string
    {
        $base = '+000' . substr(preg_replace('/\D+/', '', md5(strtolower($email))), 0, 10);

        $phone = $base;
        $i = 1;

        while (User::where('phone', $phone)->exists()) {
            $phone = $base . $i;
            $i++;
        }

        return $phone;
    }
}
