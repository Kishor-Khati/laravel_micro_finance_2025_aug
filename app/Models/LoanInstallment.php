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
    ];

    protected $casts = [
        'principal_amount' => 'decimal:2',
        'interest_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'outstanding_amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
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
}