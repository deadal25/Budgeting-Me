<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * 4 Grouped payment methods for regular / non-admin users.
     */
    public const USER_PAYMENT_METHODS = [
        'E-Wallet' => [
            'label' => 'E-Wallet',
            'desc' => 'DANA, OVO, GoPay, Grab',
            'color' => '#0284C7',
            'badge' => 'bg-sky-100 text-sky-800 border border-sky-200',
            'type' => 'Dompet Digital',
        ],
        'M-Banking' => [
            'label' => 'M-Banking',
            'desc' => 'SeaBank, BCA, Mandiri, BRI, BNI',
            'color' => '#2563EB',
            'badge' => 'bg-blue-100 text-blue-800 border border-blue-200',
            'type' => 'Mobile Banking',
        ],
        'Cash/Tunai' => [
            'label' => 'Cash/Tunai',
            'desc' => 'Uang Tunai / Dompet Fisik',
            'color' => '#10B981',
            'badge' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
            'type' => 'Uang Fisik',
        ],
        'Rekening Tabungan' => [
            'label' => 'Rekening Tabungan',
            'desc' => 'Simpanan Rekening Bank',
            'color' => '#7C3AED',
            'badge' => 'bg-purple-100 text-purple-800 border border-purple-200',
            'type' => 'Rekening Bank',
        ],
    ];

    /**
     * 4 Wallet Groups configuration for regular users.
     */
    public const USER_WALLET_GROUPS = [
        'E-Wallet' => [
            'key' => 'E-Wallet',
            'label' => 'E-Wallet',
            'desc' => 'DANA, OVO, GoPay, Grab',
            'methods' => ['E-Wallet', 'OVO', 'GoPay', 'Grab', 'DANA'],
            'color' => '#0284C7',
            'badge' => 'bg-sky-100 text-sky-800 border border-sky-200',
            'type' => 'Dompet Digital',
        ],
        'M-Banking' => [
            'key' => 'M-Banking',
            'label' => 'M-Banking',
            'desc' => 'SeaBank, BCA, Mandiri, BRI, BNI',
            'methods' => ['M-Banking', 'SeaBank', 'BRI', 'BNI', 'Mandiri', 'BCA'],
            'color' => '#2563EB',
            'badge' => 'bg-blue-100 text-blue-800 border border-blue-200',
            'type' => 'Mobile Banking',
        ],
        'Cash/Tunai' => [
            'key' => 'Cash/Tunai',
            'label' => 'Cash/Tunai',
            'desc' => 'Uang Tunai / Dompet Fisik',
            'methods' => ['Cash/Tunai', 'Cash', 'Tunai'],
            'color' => '#10B981',
            'badge' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
            'type' => 'Uang Fisik',
        ],
        'Rekening Tabungan' => [
            'key' => 'Rekening Tabungan',
            'label' => 'Rekening Tabungan',
            'desc' => 'Simpanan Rekening Bank',
            'methods' => ['Rekening Tabungan', 'Lainnya'],
            'color' => '#7C3AED',
            'badge' => 'bg-purple-100 text-purple-800 border border-purple-200',
            'type' => 'Rekening Bank',
        ],
    ];

    /**
     * Detailed payment methods for Admin & platform wide accounting.
     */
    public const PAYMENT_METHODS = [
        'E-Wallet' => [
            'label' => 'E-Wallet',
            'color' => '#0284C7',
            'badge' => 'bg-sky-100 text-sky-800 border border-sky-200',
        ],
        'M-Banking' => [
            'label' => 'M-Banking',
            'color' => '#2563EB',
            'badge' => 'bg-blue-100 text-blue-800 border border-blue-200',
        ],
        'Cash/Tunai' => [
            'label' => 'Cash/Tunai',
            'color' => '#10B981',
            'badge' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
        ],
        'Cash' => [
            'label' => 'Cash/Tunai',
            'color' => '#10B981',
            'badge' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
        ],
        'Rekening Tabungan' => [
            'label' => 'Rekening Tabungan',
            'color' => '#7C3AED',
            'badge' => 'bg-purple-100 text-purple-800 border border-purple-200',
        ],
        'SeaBank' => [
            'label' => 'SeaBank',
            'color' => '#EA580C',
            'badge' => 'bg-orange-100 text-orange-800 border border-orange-200',
        ],
        'BCA' => [
            'label' => 'BCA',
            'color' => '#2563EB',
            'badge' => 'bg-indigo-100 text-indigo-800 border border-indigo-200',
        ],
        'Mandiri' => [
            'label' => 'Mandiri',
            'color' => '#D97706',
            'badge' => 'bg-amber-100 text-amber-800 border border-amber-200',
        ],
        'BRI' => [
            'label' => 'BRI',
            'color' => '#1D4ED8',
            'badge' => 'bg-blue-100 text-blue-800 border border-blue-200',
        ],
        'BNI' => [
            'label' => 'BNI',
            'color' => '#0D9488',
            'badge' => 'bg-teal-100 text-teal-800 border border-teal-200',
        ],
        'DANA' => [
            'label' => 'DANA',
            'color' => '#0284C7',
            'badge' => 'bg-cyan-100 text-cyan-800 border border-cyan-200',
        ],
        'OVO' => [
            'label' => 'OVO',
            'color' => '#7C3AED',
            'badge' => 'bg-purple-100 text-purple-800 border border-purple-200',
        ],
        'GoPay' => [
            'label' => 'GoPay',
            'color' => '#0284C7',
            'badge' => 'bg-sky-100 text-sky-800 border border-sky-200',
        ],
        'Grab' => [
            'label' => 'Grab',
            'color' => '#059669',
            'badge' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
        ],
        'Lainnya' => [
            'label' => 'Lainnya',
            'color' => '#64748B',
            'badge' => 'bg-slate-100 text-slate-800 border border-slate-200',
        ],
    ];

    /**
     * Get available payment methods depending on user role:
     * Non-admin: Only 4 options (E-Wallet, M-Banking, Cash/Tunai, Rekening Tabungan)
     * Admin: All individual accounts
     */
    public static function getAvailablePaymentMethods(?User $user = null): array
    {
        if ($user && $user->isAdmin()) {
            return self::PAYMENT_METHODS;
        }

        return self::USER_PAYMENT_METHODS;
    }

    protected $fillable = [
        'user_id',
        'category_id',
        'type',
        'amount',
        'payment_method',
        'date',
        'notes',
        'is_protected',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'date' => 'date',
            'is_protected' => 'boolean',
        ];
    }

    /**
     * Determine if this transaction is locked from edit/delete for the specified user.
     * Default seeded demo incomes cannot be edited or deleted by the demo account.
     */
    public function isLockedFor(?User $user = null): bool
    {
        $user = $user ?? auth()->user();
        if (!$user || !$user->isDemo()) {
            return false;
        }

        return $this->type === 'income' && (bool) $this->is_protected;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Indonesian property aliases
    public function getJumlahAttribute()
    {
        return $this->amount;
    }

    public function getTanggalAttribute()
    {
        return $this->date;
    }

    public function getCatatanAttribute()
    {
        return $this->notes;
    }

    public function getTipeAttribute()
    {
        return $this->type;
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getFormattedDateAttribute(): string
    {
        return Carbon::parse($this->date)->translatedFormat('d M Y');
    }

    public function getAsalDanaAttribute(): string
    {
        return $this->payment_method ?: 'Cash/Tunai';
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        $method = $this->payment_method ?: 'Cash/Tunai';
        if ($method === 'Cash') return 'Cash/Tunai';
        return self::PAYMENT_METHODS[$method]['label'] ?? (self::USER_PAYMENT_METHODS[$method]['label'] ?? $method);
    }

    public function getPaymentMethodBadgeClassAttribute(): string
    {
        $method = $this->payment_method ?: 'Cash/Tunai';
        return self::PAYMENT_METHODS[$method]['badge'] ?? (self::USER_PAYMENT_METHODS[$method]['badge'] ?? 'bg-slate-100 text-slate-800 border border-slate-200');
    }

    public function getPaymentMethodColorAttribute(): string
    {
        $method = $this->payment_method ?: 'Cash/Tunai';
        return self::PAYMENT_METHODS[$method]['color'] ?? (self::USER_PAYMENT_METHODS[$method]['color'] ?? '#64748B');
    }

    /**
     * Get platform-wide aggregated balances, income, and expense grouped by payment method / bank account.
     */
    public static function getPlatformAccountBalances(): array
    {
        $stats = self::selectRaw('payment_method, type, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('payment_method', 'type')
            ->get();

        $result = [];
        foreach (self::PAYMENT_METHODS as $key => $meta) {
            $income = (float) ($stats->first(fn($s) => $s->payment_method === $key && $s->type === 'income')?->total ?? 0);
            $expense = (float) ($stats->first(fn($s) => $s->payment_method === $key && $s->type === 'expense')?->total ?? 0);
            $count = (int) ($stats->where('payment_method', $key)->sum('count') ?? 0);
            $balance = $income - $expense;

            $result[$key] = [
                'key' => $key,
                'label' => $meta['label'],
                'color' => $meta['color'],
                'badge' => $meta['badge'],
                'type' => in_array($key, ['Cash']) ? 'Tunai' : (in_array($key, ['OVO', 'GoPay', 'Grab', 'DANA']) ? 'E-Wallet' : (in_array($key, ['SeaBank', 'BRI', 'BNI', 'Mandiri', 'BCA']) ? 'Bank Transfer' : 'Lainnya')),
                'income' => $income,
                'expense' => $expense,
                'balance' => $balance,
                'count' => $count,
                'has_activity' => ($count > 0),
            ];
        }

        return $result;
    }
}
