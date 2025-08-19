<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class ShareBonusStatement extends Model
{
    use HasFactory;

    protected $fillable = [
        'statement_number',
        'branch_id',
        'period_start_date',
        'period_end_date',
        'generated_date',
        'total_raw_income',
        'total_expenses',
        'net_income',
        'share_bonus_percentage',
        'total_share_bonus_pool',
        'total_distributed_amount',
        'total_eligible_members',
        'total_members_received',
        'total_savings_balance',
        'financial_summary',
        'status',
        'generated_by',
        'notes',
    ];

    protected $casts = [
        'period_start_date' => 'date',
        'period_end_date' => 'date',
        'generated_date' => 'date',
        'total_raw_income' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'net_income' => 'decimal:2',
        'share_bonus_percentage' => 'decimal:2',
        'total_share_bonus_pool' => 'decimal:2',
        'total_distributed_amount' => 'decimal:2',
        'total_savings_balance' => 'decimal:2',
        'financial_summary' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->statement_number)) {
                $model->statement_number = 'SBS-' . date('Y') . '-' . str_pad(static::whereYear('created_at', date('Y'))->count() + 1, 6, '0', STR_PAD_LEFT);
            }
            if (empty($model->generated_date)) {
                $model->generated_date = now()->toDateString();
            }
            if (empty($model->generated_by)) {
                $model->generated_by = Auth::id();
            }
        });
    }

    // Relationships
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function shareBonusRecords(): HasMany
    {
        return $this->hasMany(ShareBonusRecord::class)
                    ->where('period_start_date', $this->period_start_date)
                    ->where('period_end_date', $this->period_end_date)
                    ->where('branch_id', $this->branch_id);
    }

    // Scopes
    public function scopeGenerated($query)
    {
        return $query->where('status', 'generated');
    }

    public function scopePartiallyApplied($query)
    {
        return $query->where('status', 'partially_applied');
    }

    public function scopeFullyApplied($query)
    {
        return $query->where('status', 'fully_applied');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeForPeriod($query, $startDate, $endDate)
    {
        return $query->where('period_start_date', '>=', $startDate)
                    ->where('period_end_date', '<=', $endDate);
    }

    // Helper methods
    public function isGenerated(): bool
    {
        return $this->status === 'generated';
    }

    public function isPartiallyApplied(): bool
    {
        return $this->status === 'partially_applied';
    }

    public function isFullyApplied(): bool
    {
        return $this->status === 'fully_applied';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function canBeApplied(): bool
    {
        return in_array($this->status, ['generated', 'partially_applied']);
    }

    public function canBeCancelled(): bool
    {
        return $this->status === 'generated';
    }

    public function updateDistributionStatus(): void
    {
        $appliedRecords = $this->shareBonusRecords()->applied()->count();
        $totalRecords = $this->total_eligible_members;
        
        if ($appliedRecords === 0) {
            $this->status = 'generated';
        } elseif ($appliedRecords < $totalRecords) {
            $this->status = 'partially_applied';
        } else {
            $this->status = 'fully_applied';
        }
        
        $this->total_members_received = $appliedRecords;
        $this->total_distributed_amount = $this->shareBonusRecords()->applied()->sum('bonus_amount');
        $this->save();
    }

    public function getFormattedNetIncomeAttribute(): string
    {
        return 'Rs. ' . number_format($this->net_income, 2);
    }

    public function getFormattedShareBonusPoolAttribute(): string
    {
        return 'Rs. ' . number_format($this->total_share_bonus_pool, 2);
    }

    public function getFormattedDistributedAmountAttribute(): string
    {
        return 'Rs. ' . number_format($this->total_distributed_amount, 2);
    }

    public function getDistributionPercentageAttribute(): float
    {
        if ($this->total_share_bonus_pool == 0) {
            return 0;
        }
        return ($this->total_distributed_amount / $this->total_share_bonus_pool) * 100;
    }

    public function getPeriodDescriptionAttribute(): string
    {
        return $this->period_start_date->format('M d, Y') . ' - ' . $this->period_end_date->format('M d, Y');
    }
}
