<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDemo(): bool
    {
        return in_array($this->email, ['user@budgetingme.com', 'siti@budgetingme.com']);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function debts()
    {
        return $this->hasMany(Debt::class);
    }

    public function getTotalUnpaidDebtAttribute(): float
    {
        return (float) $this->debts()
            ->where('status', '!=', 'paid')
            ->get()
            ->sum('remaining_amount');
    }

    public function getUnpaidDebtsCountAttribute(): int
    {
        return (int) $this->debts()
            ->where('status', '!=', 'paid')
            ->count();
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public function getTotalIncomeAttribute(): float
    {
        return (float) $this->transactions()->where('type', 'income')->sum('amount');
    }

    public function getTotalExpenseAttribute(): float
    {
        return (float) $this->transactions()->where('type', 'expense')->sum('amount');
    }

    public function getBalanceAttribute(): float
    {
        return $this->total_income - $this->total_expense;
    }

    /**
     * Get aggregated balances, income, and expense grouped by payment method / bank account.
     */
    public function getAccountBalances(): array
    {
        $stats = $this->transactions()
            ->selectRaw('payment_method, type, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('payment_method', 'type')
            ->get();

        $result = [];
        foreach (Transaction::PAYMENT_METHODS as $key => $meta) {
            $income = (float) ($stats->first(fn($s) => $s->payment_method === $key && $s->type === 'income')?->total ?? 0);
            $expense = (float) ($stats->first(fn($s) => $s->payment_method === $key && $s->type === 'expense')?->total ?? 0);
            $count = (int) ($stats->where('payment_method', $key)->sum('count') ?? 0);
            $balance = $income - $expense;

            $result[$key] = [
                'key' => $key,
                'label' => $meta['label'],
                'color' => $meta['color'],
                'badge' => $meta['badge'],
                'type' => in_array($key, ['Cash']) ? 'Tunai' : (in_array($key, ['OVO', 'GoPay', 'Grab', 'DANA', 'E-Wallet']) ? 'E-Wallet' : (in_array($key, ['SeaBank', 'BRI', 'BNI', 'Mandiri', 'BCA', 'M-Banking']) ? 'Bank Transfer' : (in_array($key, ['Rekening Tabungan']) ? 'Rekening Tabungan' : 'Lainnya'))),
                'income' => $income,
                'expense' => $expense,
                'balance' => $balance,
                'count' => $count,
                'has_activity' => ($count > 0),
            ];
        }

        return $result;
    }

    /**
     * Get aggregated 4-group balances for regular users: E-Wallet, M-Banking, Cash/Tunai, Rekening Tabungan.
     */
    public function getUserGroupedAccountBalances(): array
    {
        $stats = $this->transactions()
            ->selectRaw('payment_method, type, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('payment_method', 'type')
            ->get();

        $result = [];
        foreach (Transaction::USER_WALLET_GROUPS as $groupKey => $group) {
            $methods = $group['methods'];
            $income = (float) ($stats->whereIn('payment_method', $methods)->where('type', 'income')->sum('total') ?? 0);
            $expense = (float) ($stats->whereIn('payment_method', $methods)->where('type', 'expense')->sum('total') ?? 0);
            $count = (int) ($stats->whereIn('payment_method', $methods)->sum('count') ?? 0);
            $balance = $income - $expense;

            $result[$groupKey] = [
                'key' => $groupKey,
                'label' => $group['label'],
                'desc' => $group['desc'],
                'color' => $group['color'],
                'badge' => $group['badge'],
                'type' => $group['type'],
                'income' => $income,
                'expense' => $expense,
                'balance' => $balance,
                'count' => $count,
                'has_activity' => ($count > 0 || $balance != 0),
            ];
        }

        return $result;
    }

    /**
     * Get appropriate account balance cards based on user role:
     * - Admin sees all individual banks/wallets
     * - Regular users see 4 grouped categories (E-Wallet, M-Banking, Cash/Tunai, Rekening Tabungan)
     */
    public function getDisplayAccountBalances(): array
    {
        if ($this->isAdmin()) {
            return $this->getAccountBalances();
        }

        return $this->getUserGroupedAccountBalances();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
