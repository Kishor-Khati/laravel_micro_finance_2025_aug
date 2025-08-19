<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_number',
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'gender',
        'citizenship_number',
        'phone',
        'email',
        'address',
        'occupation',
        'monthly_income',
        'branch_id',
        'guardian_name',
        'guardian_phone',
        'guardian_relation',
        'status',
        'kyc_status',
        'kyc_documents',
        'membership_date',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'membership_date' => 'date',
        'monthly_income' => 'decimal:2',
        'kyc_documents' => 'array',
    ];

    // Relationships
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function savingsAccounts(): HasMany
    {
        return $this->hasMany(SavingsAccount::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name);
    }

    public function getMemberIdAttribute(): string
    {
        return $this->member_number;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeKycVerified($query)
    {
        return $query->where('kyc_status', 'verified');
    }
}