<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'creditor',
        'amount',
        'paid_amount',
        'due_date',
        'pay_on_salary',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'due_date' => 'date',
            'pay_on_salary' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float) $this->amount - (float) $this->paid_amount);
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'paid' || $this->remaining_amount <= 0;
    }

    public function getPercentagePaidAttribute(): int
    {
        if ($this->amount <= 0) {
            return 100;
        }

        return (int) min(100, round(($this->paid_amount / $this->amount) * 100));
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getFormattedPaidAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->paid_amount, 0, ',', '.');
    }

    public function getFormattedRemainingAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->remaining_amount, 0, ',', '.');
    }

    public function getFormattedDueDateAttribute(): ?string
    {
        if (!$this->due_date) {
            return null;
        }

        return Carbon::parse($this->due_date)->translatedFormat('d M Y');
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->is_paid) {
            return 'Lunas';
        }

        if ($this->paid_amount > 0) {
            return 'Dicicil Sebagian';
        }

        return 'Belum Lunas';
    }

    public function getStatusBadgeAttribute(): string
    {
        if ($this->is_paid) {
            return 'bg-emerald-100 text-emerald-800 border-emerald-200';
        }

        if ($this->paid_amount > 0) {
            return 'bg-amber-100 text-amber-800 border-amber-200';
        }

        return 'bg-rose-100 text-rose-800 border-rose-200';
    }

    /**
     * Record a repayment on this debt.
     */
    public function recordPayment(float $payment): void
    {
        $newPaid = (float) $this->paid_amount + $payment;
        $this->paid_amount = $newPaid;

        if ($this->paid_amount >= $this->amount) {
            $this->status = 'paid';
            $this->paid_amount = $this->amount;
        } else {
            $this->status = 'partial';
        }

        $this->save();
    }
}
