<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_number',
        'member_id',
        'branch_id',
        'transaction_type',
        'amount',
        'balance_before',
        'balance_after',
        'reference_type',
        'reference_id',
        'description',
        'processed_by',
        'transaction_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    // Relationships
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function reference()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeDeposits($query)
    {
        return $query->where('transaction_type', 'deposit');
    }

    public function scopeWithdrawals($query)
    {
        return $query->where('transaction_type', 'withdrawal');
    }

    public function scopeLoanTransactions($query)
    {
        return $query->whereIn('transaction_type', ['loan_disbursement', 'loan_payment']);
    }
}