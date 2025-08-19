<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanInstallment extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'installment_number',
        'principal_amount',
        'interest_amount',
        'total_amount',
        'paid_amount',
        'outstanding_amount',
        'due_date',
        'paid_date',
        'status',
        'remarks',
        'penalty_amount',
        'penalty_rate',
        'days_overdue',
        'penalty_calculated_date',
        'penalty_waived',
        'penalty_remarks',
    ];

    protected $casts = [
        'principal_amount' => 'decimal:2',
        'interest_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'outstanding_amount' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'penalty_rate' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
        'penalty_calculated_date' => 'date',
        'penalty_waived' => 'boolean',
    ];

    // Relationships
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    // Scopes
    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
                    ->orWhere(function ($q) {
                        $q->where('status', 'pending')
                          ->where('due_date', '<', now());
                    });
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // Methods
    public function isOverdue(): bool
    {
        return $this->status === 'overdue' || 
               ($this->status === 'pending' && $this->due_date < now());
    }

    public function calculateDaysOverdue(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        
        return now()->diffInDays($this->due_date);
    }

    public function calculatePenalty(float $dailyPenaltyRate = 0.1): float
    {
        if (!$this->isOverdue() || $this->penalty_waived) {
            return 0;
        }

        $daysOverdue = $this->calculateDaysOverdue();
        $penaltyAmount = ($this->outstanding_amount * $dailyPenaltyRate / 100) * $daysOverdue;
        
        return round($penaltyAmount, 2);
    }

    public function updatePenalty(float $dailyPenaltyRate = 0.1): void
    {
        if ($this->isOverdue() && !$this->penalty_waived) {
            $this->update([
                'days_overdue' => $this->calculateDaysOverdue(),
                'penalty_rate' => $dailyPenaltyRate,
                'penalty_amount' => $this->calculatePenalty($dailyPenaltyRate),
                'penalty_calculated_date' => now()->toDateString(),
                'status' => 'overdue'
            ]);
        }
    }

    public function getTotalAmountWithPenalty(): float
    {
        return $this->outstanding_amount + $this->penalty_amount;
    }

    public function waivePenalty(string $reason = null): void
    {
        $this->update([
            'penalty_waived' => true,
            'penalty_remarks' => $reason ?? 'Penalty waived by admin',
            'penalty_amount' => 0
        ]);
    }
}