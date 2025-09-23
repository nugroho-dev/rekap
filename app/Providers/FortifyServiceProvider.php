<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Laravel\Fortify\Contracts\ResetPasswordViewResponse as ResetPasswordViewResponseContract;
use Laravel\Fortify\Contracts\LoginViewResponse as LoginViewResponseContract;
use App\Http\Responses\LoginViewResponse;
use App\Http\Responses\ResetPasswordViewResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // bind Fortify login view response contract
        $this->app->singleton(LoginViewResponseContract::class, LoginViewResponse::class);

        // bind Fortify reset-password view response contract
        $this->app->singleton(ResetPasswordViewResponseContract::class, ResetPasswordViewResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        Fortify::loginView(fn() => view('login.index'));

        // explicit view for password reset page
        Fortify::resetPasswordView(fn() => view('auth.reset-password'));

        // register forgot-password (request reset link) view
        Fortify::requestPasswordResetLinkView(fn() => view('auth.forgot-password'));

        Fortify::authenticateUsing(function (Request $request) {
            Log::debug('login.attempt', [
                'email' => $request->input('email'),
                'has_recaptcha' => (bool) $request->input('g-recaptcha-response'),
                'ip' => $request->ip(),
            ]);

            $recaptcha = $request->input('g-recaptcha-response');
            $secret = config('services.recaptcha.secret');

            if (empty($recaptcha) || empty($secret)) {
                Session::flash('recaptcha_error', 'reCAPTCHA tidak terisi atau belum dikonfigurasi.');
                return null;
            }

            // Avoid verifying same token twice in same request
            if ($request->attributes->get('recaptcha_verified')) {
                Log::debug('recaptcha.skip', ['reason' => 'already_verified_in_request']);
            } else {
                // prevent reuse across requests by short-lived cache of token hash
                $tokenHash = hash_hmac('sha256', $recaptcha, config('app.key'));

                // if token already used recently -> duplicate
                if (\Illuminate\Support\Facades\Cache::has('recaptcha_token_'.$tokenHash)) {
                    Session::flash('recaptcha_error', 'Token reCAPTCHA sudah digunakan. Muat ulang halaman dan coba lagi.');
                    Log::info('recaptcha.reuse_detected', ['token_hash' => substr($tokenHash,0,16)]);
                    return null;
                }

                $resp = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $secret,
                    'response' => $recaptcha,
                    'remoteip' => $request->ip(),
                ]);

                Log::info('recaptcha.verify', ['ok' => $resp->ok(), 'body' => $resp->json()]);

                if (! $resp->ok()) {
                    Session::flash('recaptcha_error', 'Gagal memverifikasi reCAPTCHA (http error).');
                    return null;
                }

                $json = $resp->json();
                if (! data_get($json, 'success')) {
                    $errors = data_get($json, 'error-codes', []);
                    if (in_array('timeout-or-duplicate', $errors, true)) {
                        Session::flash('recaptcha_error', 'Token reCAPTCHA kedaluwarsa atau sudah digunakan. Muat ulang halaman dan coba lagi.');
                    } else {
                        Session::flash('recaptcha_error', 'Verifikasi reCAPTCHA gagal: '.implode(', ', $errors));
                    }
                    return null;
                }

                // mark token as used for short time to avoid reuse (TTL 2 minutes)
                \Illuminate\Support\Facades\Cache::put('recaptcha_token_'.$tokenHash, true, now()->addMinutes(2));

                // mark request as verified so other code in same request won't reverify
                $request->attributes->set('recaptcha_verified', true);
            }

            // lanjutkan cek kredensial (email + password)
            $user = User::where('email', $request->input('email'))->first();

            Log::debug('login.lookup', [
                'found' => (bool) $user,
                'hash_length' => $user ? strlen($user->password) : null,
                'password_input_present' => $request->filled('password'),
            ]);

            if ($user) {
                $ok = Hash::check($request->input('password'), $user->password);
                Log::debug('login.hashcheck', ['ok' => $ok]);
                if ($ok) return $user;
            }

            return null;
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
