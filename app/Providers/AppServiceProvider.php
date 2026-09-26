<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure writable paths and serverless-safe drivers on Vercel
        if (isset($_SERVER['VERCEL']) || getenv('VERCEL') || file_exists('/tmp')) {
            $viewsDir = '/tmp/storage/framework/views';
            if (!is_dir($viewsDir)) {
                @mkdir($viewsDir, 0755, true);
            }
            config([
                'view.compiled' => $viewsDir,
                'session.driver' => 'cookie',
                'cache.default' => 'array',
                'app.maintenance.driver' => 'file',
            ]);
        }

        // Auto-initialize SQLite database on serverless / Vercel environments if tables are missing
        if (config('database.default') === 'sqlite') {
            try {
                $tmpDb = '/tmp/database.sqlite';
                if (file_exists($tmpDb)) {
                    config(['database.connections.sqlite.database' => $tmpDb]);
                }

                if (!\Illuminate\Support\Facades\Schema::hasTable('users')) {
                    \Illuminate\Support\Facades\Artisan::call('migrate --force');
                    \Illuminate\Support\Facades\Artisan::call('db:seed --force');
                }
            } catch (\Throwable $e) {
                // Ignore during early migrations or test setups
            }
        }

        // Custom Indonesian Password Reset Email with Direct Action Link
        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            $resetUrl = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject('Permintaan Reset Kata Sandi - BudgetingMe')
                ->greeting('Halo, ' . ($notifiable->name ?? 'Pengguna BudgetingMe') . '!')
                ->line('Kami menerima permintaan untuk mereset kata sandi akun BudgetingMe yang terdaftar dengan email ini.')
                ->action('Ubah Kata Sandi Sekarang', $resetUrl)
                ->line('Tautan di atas akan kedaluwarsa secara otomatis dalam waktu 60 menit.')
                ->line('Jika Anda tidak merasa mengajukan permintaan ini, silakan abaikan email ini. Akun Anda tetap aman.')
                ->salutation("Salam hangat,\nTim Pengembang BudgetingMe");
        });
    }
}
