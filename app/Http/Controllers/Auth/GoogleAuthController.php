<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse|View
    {
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');

        if (empty($clientId) || empty($clientSecret)) {
            if (app()->isLocal() || app()->environment('testing')) {
                return view('auth.google-dev');
            }

            return redirect()->route('login')->withErrors([
                'email' => 'Integrasi Google Sign-In belum dikonfigurasi. Harap lengkapi GOOGLE_CLIENT_ID dan GOOGLE_CLIENT_SECRET pada file .env.',
            ]);
        }

        return Socialite::driver('google')->redirect();
    }

    public function devLogin(Request $request): RedirectResponse
    {
        abort_unless(app()->isLocal() || app()->environment('testing'), 403);

        $request->validate([
            'email' => ['required', 'email'],
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $email = strtolower(trim($request->input('email')));
        $rawName = $request->input('name') ?: explode('@', $email)[0];
        $name = ucwords(str_replace(['.', '_', '-'], ' ', $rawName));

        $user = User::where('email', $email)->first();

        if (! $user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'google_id' => 'mock-google-'.md5($email),
                'avatar' => 'https://ui-avatars.com/api/?name='.urlencode($name).'&background=10b981&color=fff',
                'email_verified_at' => now(),
                'currency' => 'IDR',
            ]);
        } else {
            if (empty($user->google_id)) {
                $user->update([
                    'google_id' => 'mock-google-'.md5($email),
                    'avatar' => $user->avatar ?: 'https://ui-avatars.com/api/?name='.urlencode($name).'&background=10b981&color=fff',
                ]);
            }
        }

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Selamat datang, '.$user->name.'! Anda berhasil masuk (Akun Google).');
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Gagal melakukan otentikasi dengan akun Google: '.$e->getMessage(),
            ]);
        }

        // 1. Cari user berdasarkan google_id
        $user = User::where('google_id', $googleUser->getId())->first();

        if (! $user) {
            // 2. Jika belum ada google_id, cari berdasarkan email
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Tautkan akun yang sudah ada ke akun Google
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $user->avatar ?: $googleUser->getAvatar(),
                    'email_verified_at' => $user->email_verified_at ?: now(),
                ]);
            } else {
                // 3. Daftarkan user baru secara otomatis
                $name = $googleUser->getName() ?: explode('@', $googleUser->getEmail())[0];

                $user = User::create([
                    'name' => $name,
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                    'currency' => 'IDR',
                ]);
            }
        } else {
            // Update avatar terbaru jika ada
            if ($googleUser->getAvatar() && $user->avatar !== $googleUser->getAvatar()) {
                $user->update(['avatar' => $googleUser->getAvatar()]);
            }
        }

        Auth::login($user, remember: true);
        request()->session()->regenerate();

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Selamat datang, '.$user->name.'! Anda berhasil masuk dengan akun Google.');
    }
}
