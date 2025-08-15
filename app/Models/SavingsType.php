<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SavingsType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'min_balance',
        'interest_rate',
        'withdrawal_limit_per_month',
        'withdrawal_limit_amount',
        'is_mandatory',
        'status',
    ];

    protected $casts = [
        'min_balance' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'withdrawal_limit_amount' => 'decimal:2',
        'is_mandatory' => 'boolean',
    ];

    // Relationships
    public function savingsAccounts(): HasMany
    {
        return $this->hasMany(SavingsAccount::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }
}