<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_number',
        'member_id',
        'loan_type_id',
        'branch_id',
        'requested_amount',
        'approved_amount',
        'interest_rate',
        'duration_months',
        'monthly_installment',
        'purpose',
        'collateral',
        'status',
        'application_date',
        'approved_date',
        'disbursed_date',
        'maturity_date',
        'approved_by',
        'remarks',
    ];

    protected $casts = [
        'requested_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'monthly_installment' => 'decimal:2',
        'application_date' => 'date',
        'approved_date' => 'date',
        'disbursed_date' => 'date',
        'maturity_date' => 'date',
    ];

    // Relationships
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function loanType(): BelongsTo
    {
        return $this->belongsTo(LoanType::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function installments(): HasMany
    {
        return $this->hasMany(LoanInstallment::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'reference_id')
                    ->where('reference_type', 'loan');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['approved', 'disbursed']);
    }

    public function scopeOverdue($query)
    {
        return $query->whereHas('installments', function ($q) {
            $q->where('status', 'overdue');
        });
    }

    // Methods
    public function getTotalPaidAmount(): float
    {
        return $this->installments()->sum('paid_amount');
    }

    public function getOutstandingAmount(): float
    {
        return $this->approved_amount - $this->getTotalPaidAmount();
    }
}