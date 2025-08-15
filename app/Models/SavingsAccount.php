<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SavingsAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_number',
        'member_id',
        'savings_type_id',
        'branch_id',
        'balance',
        'interest_earned',
        'opened_date',
        'status',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'interest_earned' => 'decimal:2',
        'opened_date' => 'date',
    ];

    // Relationships
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function savingsType(): BelongsTo
    {
        return $this->belongsTo(SavingsType::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'reference_id')
                    ->where('reference_type', 'savings_account');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}