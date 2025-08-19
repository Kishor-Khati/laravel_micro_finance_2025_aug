<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ShareBonusRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'record_number',
        'member_id',
        'savings_account_id',
        'branch_id',
        'bonus_amount',
        'savings_balance_at_calculation',
        'proportion_percentage',
        'calculation_date',
        'period_start_date',
        'period_end_date',
        'total_net_income',
        'share_bonus_percentage',
        'total_share_bonus_pool',
        'status',
        'applied_at',
        'reversed_at',
        'calculated_by',
        'applied_by',
        'reversed_by',
        'notes',
    ];

    protected $casts = [
        'bonus_amount' => 'decimal:2',
        'savings_balance_at_calculation' => 'decimal:2',
        'proportion_percentage' => 'decimal:4',
        'total_net_income' => 'decimal:2',
        'share_bonus_percentage' => 'decimal:2',
        'total_share_bonus_pool' => 'decimal:2',
        'calculation_date' => 'date',
        'period_start_date' => 'date',
        'period_end_date' => 'date',
        'applied_at' => 'datetime',
        'reversed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->record_number)) {
                $model->record_number = 'SBR-' . date('Y') . '-' . str_pad(static::whereYear('created_at', date('Y'))->count() + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    // Relationships
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function savingsAccount(): BelongsTo
    {
        return $this->belongsTo(SavingsAccount::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function calculatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'calculated_by');
    }

    public function appliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applied_by');
    }

    public function reversedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reversed_by');
    }

    // Scopes
    public function scopeCalculated($query)
    {
        return $query->where('status', 'calculated');
    }

    public function scopeApplied($query)
    {
        return $query->where('status', 'applied');
    }

    public function scopeReversed($query)
    {
        return $query->where('status', 'reversed');
    }

    public function scopeForPeriod($query, $startDate, $endDate)
    {
        return $query->where('period_start_date', '>=', $startDate)
                    ->where('period_end_date', '<=', $endDate);
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    // Helper methods
    public function isCalculated(): bool
    {
        return $this->status === 'calculated';
    }

    public function isApplied(): bool
    {
        return $this->status === 'applied';
    }

    public function isReversed(): bool
    {
        return $this->status === 'reversed';
    }

    public function canBeApplied(): bool
    {
        return $this->status === 'calculated';
    }

    public function canBeReversed(): bool
    {
        return $this->status === 'applied';
    }

    public function markAsApplied($appliedBy = null): bool
    {
        if (!$this->canBeApplied()) {
            return false;
        }

        return $this->update([
            'status' => 'applied',
            'applied_at' => now(),
            'applied_by' => $appliedBy ?? Auth::id(),
        ]);
    }

    public function markAsReversed($reversedBy = null): bool
    {
        if (!$this->canBeReversed()) {
            return false;
        }

        return $this->update([
            'status' => 'reversed',
            'reversed_at' => now(),
            'reversed_by' => $reversedBy ?? Auth::id(),
        ]);
    }

    public function getFormattedBonusAmountAttribute(): string
    {
        return 'Rs. ' . number_format($this->bonus_amount, 2);
    }

    public function getFormattedProportionPercentageAttribute(): string
    {
        return number_format($this->proportion_percentage, 4) . '%';
    }
}
