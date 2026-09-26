<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'month',
        'year',
        'limit_amount',
        'is_protected',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'integer',
            'year' => 'integer',
            'limit_amount' => 'decimal:2',
            'is_protected' => 'boolean',
        ];
    }

    /**
     * Determine if this budget is locked from edit/delete for the specified user.
     * Default seeded demo budgets cannot be edited or deleted by the demo account.
     */
    public function isLockedFor(?User $user = null): bool
    {
        $user = $user ?? auth()->user();
        if (!$user || !$user->isDemo()) {
            return false;
        }

        return (bool) $this->is_protected;
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
    public function getBulanAttribute()
    {
        return $this->month;
    }

    public function getTahunAttribute()
    {
        return $this->year;
    }

    public function getMonthNameAttribute(): string
    {
        return Carbon::create()->month($this->month)->locale('id')->isoFormat('MMMM');
    }

    public function getSpentAmountAttribute(): float
    {
        $query = Transaction::query()
            ->where('user_id', $this->user_id)
            ->where('type', 'expense')
            ->whereYear('date', $this->year)
            ->whereMonth('date', $this->month);

        if ($this->category_id) {
            $query->where('category_id', $this->category_id);
        }

        return (float) $query->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return (float) ($this->limit_amount - $this->spent_amount);
    }

    public function getPercentageAttribute(): float
    {
        if ($this->limit_amount <= 0) {
            return 0;
        }
        return round(($this->spent_amount / $this->limit_amount) * 100, 1);
    }

    public function getStatusAttribute(): string
    {
        $percentage = $this->percentage;
        if ($percentage < 70) {
            return 'safe'; // green
        } elseif ($percentage < 100) {
            return 'warning'; // yellow
        } else {
            return 'danger'; // red
        }
    }

    public function getStatusLabelAttribute(): string
    {
        $percentage = $this->percentage;
        if ($percentage < 70) {
            return 'Aman (<70%)';
        } elseif ($percentage < 100) {
            return 'Hati-hati jangan boros (70-99%)';
        } else {
            return 'Limit Terlampaui (≥100%)';
        }
    }

    public function getFormattedLimitAttribute(): string
    {
        return 'Rp ' . number_format($this->limit_amount, 0, ',', '.');
    }

    public function getFormattedSpentAttribute(): string
    {
        return 'Rp ' . number_format($this->spent_amount, 0, ',', '.');
    }

    public function getFormattedRemainingAttribute(): string
    {
        return 'Rp ' . number_format($this->remaining_amount, 0, ',', '.');
    }
}
