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
                'hashing.driver' => 'bcrypt',
                'hashing.bcrypt.rounds' => 12,
                'hashing.rehash_on_login' => false,
            ]);
        }

        // Auto-initialize SQLite database and ensure users exist on serverless / Vercel environments
        if (config('database.default') === 'sqlite') {
            try {
                $tmpDb = '/tmp/database.sqlite';
                $defaultSqlite = database_path('database.sqlite');
                if (file_exists($defaultSqlite) && filesize($defaultSqlite) > 0) {
                    if (!file_exists($tmpDb) || filesize($tmpDb) < filesize($defaultSqlite)) {
                        @copy($defaultSqlite, $tmpDb);
                    }
                }
                if (file_exists($tmpDb)) {
                    @chmod($tmpDb, 0666);
                    config(['database.connections.sqlite.database' => $tmpDb]);
                    \Illuminate\Support\Facades\DB::purge('sqlite');
                }

                if (!\Illuminate\Support\Facades\Schema::hasTable('users')) {
                    \Illuminate\Support\Facades\Artisan::call('migrate --force');
                }

                // 1. Ensure Admin Account (Alqadri - with and without dot) always exists and has admin role
                $adminEmails = ['alqad.ri2505@gmail.com', 'alqadri2505@gmail.com'];
                foreach ($adminEmails as $adminEmail) {
                    $admin = \App\Models\User::firstOrNew(['email' => $adminEmail]);
                    if (!$admin->exists) {
                        $admin->name = 'Alqadri (Admin)';
                        $admin->password = \Illuminate\Support\Facades\Hash::make('password');
                        $admin->role = 'admin';
                        $admin->email_verified_at = now();
                        $admin->save();
                    } else {
                        // Ensure it has admin role
                        if ($admin->role !== 'admin') {
                            $admin->role = 'admin';
                            $admin->save();
                        }
                    }
                }

                // 2. Ensure Demo User (Budi Santoso) exists
                $demoUser = \App\Models\User::firstOrNew(['email' => 'user@budgetingme.com']);
                if (!$demoUser->exists || !\Illuminate\Support\Facades\Hash::check('password', $demoUser->password)) {
                    $demoUser->name = 'Budi Santoso';
                    $demoUser->password = \Illuminate\Support\Facades\Hash::make('password');
                    $demoUser->role = 'user';
                    $demoUser->email_verified_at = now();
                    $demoUser->save();
                }

                // 3. Ensure Categories are seeded if empty
                if (\Illuminate\Support\Facades\Schema::hasTable('categories') && \App\Models\Category::count() === 0) {
                    \Illuminate\Support\Facades\Artisan::call('db:seed --class=CategorySeeder --force');
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Auto DB init error: ' . $e->getMessage());
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
