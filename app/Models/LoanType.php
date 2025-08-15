<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoanType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'min_amount',
        'max_amount',
        'interest_rate',
        'min_duration_months',
        'max_duration_months',
        'interest_type',
        'status',
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
    ];

    // Relationships
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}