<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    use HasFactory;

    public const KEY_REGISTRATION_CODE = 'registration_code';

    protected $fillable = [
        'key',
        'value',
        'description',
        'expires_at',
        'last_generated_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'last_generated_at' => 'datetime',
        ];
    }

    /**
     * Generate an unambiguous 4-character alphanumeric uppercase code.
     */
    public static function generateRandomCode(int $length = 4): string
    {
        // Characters excluding ambiguous 0/O and 1/I
        $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $code = '';
        $maxIndex = strlen($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[random_int(0, $maxIndex)];
        }
        return $code;
    }

    /**
     * Get or automatically rotate the active 4-character registration code (valid for 7 days / 1 week).
     */
    public static function getActiveRegistrationCode(): string
    {
        $setting = self::firstOrNew(['key' => 'registration_code']);

        // Check if code doesn't exist yet OR has expired (past 7 days)
        if (!$setting->exists || !$setting->value || ($setting->expires_at && Carbon::now()->greaterThanOrEqualTo($setting->expires_at))) {
            $setting->value = self::generateRandomCode(4);
            $setting->description = 'Kode Registrasi Pengguna Baru (Berputar Otomatis Setiap Minggu / 7 Hari)';
            $setting->last_generated_at = Carbon::now();
            $setting->expires_at = Carbon::now()->addDays(7);
            $setting->save();
        }

        return $setting->value;
    }

    /**
     * Get complete details for the active registration code for the Admin display.
     */
    public static function getRegistrationCodeDetails(): array
    {
        // Ensure active code is evaluated and rotated if expired
        $code = self::getActiveRegistrationCode();
        $setting = self::where('key', 'registration_code')->first();

        $expiresAt = $setting?->expires_at;
        $lastGeneratedAt = $setting?->last_generated_at;

        if ($expiresAt) {
            $now = Carbon::now();
            $diffHours = (int) $now->diffInHours($expiresAt, false);
            $hoursRemaining = max(0, $diffHours);
            $daysRemaining = max(0, (int) ceil($diffHours / 24));
        }

        $remainingText = $daysRemaining > 1
            ? "{$daysRemaining} hari lagi"
            : ($daysRemaining === 1 ? "1 hari lagi" : ($hoursRemaining > 0 ? "{$hoursRemaining} jam lagi" : "Segera berakhir"));

        $formattedExpiresAt = $expiresAt ? $expiresAt->locale('id')->isoFormat('dddd, D MMMM Y [pukul] HH:mm') : '-';
        $formattedLastGenerated = $lastGeneratedAt ? $lastGeneratedAt->locale('id')->isoFormat('D MMMM Y [pukul] HH:mm') : '-';

        return [
            'code' => $code,
            'expires_at' => $expiresAt,
            'last_generated_at' => $lastGeneratedAt,
            'formatted_expires_at' => $formattedExpiresAt,
            'expires_at_formatted' => $formattedExpiresAt,
            'formatted_last_generated' => $formattedLastGenerated,
            'last_generated_at_formatted' => $formattedLastGenerated,
            'days_remaining' => $daysRemaining,
            'remaining_days' => $daysRemaining,
            'hours_remaining' => $hoursRemaining,
            'remaining_hours' => $hoursRemaining,
            'remaining_text' => $remainingText,
        ];
    }

    /**
     * Force generate a new random 4-character registration code and reset the 7-day period.
     */
    public static function regenerateRegistrationCode(): string
    {
        $setting = self::firstOrNew(['key' => 'registration_code']);
        $newCode = self::generateRandomCode(4);

        $setting->value = $newCode;
        $setting->description = 'Kode Registrasi Pengguna Baru (Berputar Otomatis Setiap Minggu / 7 Hari)';
        $setting->last_generated_at = Carbon::now();
        $setting->expires_at = Carbon::now()->addDays(7);
        $setting->save();

        return $newCode;
    }

    /**
     * Set a custom 4-character registration code manually by Admin.
     */
    public static function setCustomRegistrationCode(string $code): string
    {
        $cleanCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $code), 0, 4));
        if (strlen($cleanCode) < 4) {
            $cleanCode = str_pad($cleanCode, 4, '9');
        }

        $setting = self::firstOrNew(['key' => 'registration_code']);
        $setting->value = $cleanCode;
        $setting->description = 'Kode Registrasi Pengguna Baru (Berputar Otomatis Setiap Minggu / 7 Hari)';
        $setting->last_generated_at = Carbon::now();
        $setting->expires_at = Carbon::now()->addDays(7);
        $setting->save();

        return $cleanCode;
    }

    /**
     * Validate the user-entered registration code.
     */
    public static function validateRegistrationCode(?string $input): bool
    {
        if (empty($input)) {
            return false;
        }

        $activeCode = self::getActiveRegistrationCode();
        return strtoupper(trim($input)) === strtoupper(trim($activeCode));
    }
}
