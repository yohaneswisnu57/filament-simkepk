<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            /** @var SocialiteUser $googleUser */
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $exception) {
            Log::warning('Google login failed.', ['exception' => $exception->getMessage()]);

            return redirect()->route('filament.user.auth.login')
                ->withErrors(['email' => 'Login dengan Google gagal atau dibatalkan. Silakan coba lagi.']);
        }

        $user = $this->resolveUser($googleUser);

        if (! $user->is_active) {
            return redirect()->route('filament.user.auth.login')
                ->withErrors(['email' => 'Akun Anda tidak aktif. Silakan hubungi administrator.']);
        }

        Auth::guard('web')->login($user, remember: true);

        return redirect()->intended('/user');
    }

    private function resolveUser(SocialiteUser $googleUser): User
    {
        $existingByGoogleId = User::query()->where('google_id', $googleUser->getId())->first();

        if ($existingByGoogleId) {
            return $existingByGoogleId;
        }

        $existingByEmail = User::query()->where('email', $googleUser->getEmail())->first();

        if ($existingByEmail) {
            $existingByEmail->forceFill([
                'google_id' => $googleUser->getId(),
                'provider' => 'google',
            ])->save();

            return $existingByEmail;
        }

        return DB::transaction(function () use ($googleUser): User {
            $user = User::create([
                'name' => $googleUser->getName() ?? $googleUser->getNickname(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'provider' => 'google',
                'password' => Hash::make(Str::random(40)),
                'is_active' => true,
            ]);

            $user->assignRole('user');

            return $user;
        });
    }
}
