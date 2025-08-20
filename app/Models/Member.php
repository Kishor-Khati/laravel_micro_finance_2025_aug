<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_number',
        'member_number_auto_generated',
        'full_name', // Combined name field
        'first_name', // Keep for backward compatibility
        'middle_name',
        'last_name',
        'date_of_birth',
        'gender',
        'citizenship_number',
        'phone',
        'phone_secondary',
        'email',
        'profile_image',
        'address',
        'occupation',
        'monthly_income',
        'branch_id',
        'guardian_name',
        'guardian_phone',
        'guardian_relation',
        'family_members',
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
        'family_members' => 'array',
        'member_number_auto_generated' => 'boolean',
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

    // Family members relationship (self-referencing many-to-many)
    public function familyMembersList(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'member_family', 'member_id', 'family_member_id');
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        // If full_name is set, use it; otherwise, combine individual name parts
        if ($this->attributes['full_name'] ?? null) {
            return $this->attributes['full_name'];
        }
        return trim($this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name);
    }

    public function getMemberIdAttribute(): string
    {
        return $this->member_number;
    }

    public function getProfileImageUrlAttribute(): ?string
    {
        if ($this->profile_image && file_exists(public_path('images/member-img/' . $this->profile_image))) {
            return asset('images/member-img/' . $this->profile_image);
        }
        return null;
    }

    /**
     * Get KYC document URLs
     */
    public function getKycDocumentUrlsAttribute(): array
    {
        if (!$this->kyc_documents) {
            return [];
        }
        
        $urls = [];
        foreach ($this->kyc_documents as $filename) {
            if (file_exists(public_path('images/kyc-docs/' . $filename))) {
                $urls[] = asset('images/kyc-docs/' . $filename);
            }
        }
        
        return $urls;
    }
    
    /**
     * Check if a KYC document file exists
     */
    public function kycDocumentExists($filename): bool
    {
        return file_exists(public_path('images/kyc-docs/' . $filename));
    }
    
    public function getAvatarInitialsAttribute(): string
    {
        $firstName = $this->full_name ?? $this->first_name ?? 'M';
        $lastName = $this->last_name ?? '';
        
        return strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
    }

    // Mutators
    public function setFullNameAttribute($value)
    {
        $this->attributes['full_name'] = $value;
        
        // Auto-split full name into parts if individual parts are not set
        if ($value && !$this->first_name) {
            $nameParts = explode(' ', trim($value));
            $this->attributes['first_name'] = $nameParts[0] ?? '';
            $this->attributes['last_name'] = end($nameParts) ?? '';
            
            if (count($nameParts) > 2) {
                $this->attributes['middle_name'] = implode(' ', array_slice($nameParts, 1, -1));
            }
        }
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

    // Static methods
    public static function generateMemberNumber(): string
    {
        // Find the highest existing member number
        $lastMember = static::orderBy('member_number', 'desc')
            ->where('member_number', 'REGEXP', '^MEM[0-9]{5}$')
            ->first();
        
        if ($lastMember) {
            // Extract the numeric part and increment
            $lastNumber = (int) substr($lastMember->member_number, 3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        // Generate new number and ensure it's unique
        do {
            $memberNumber = 'MEM' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
            $exists = static::where('member_number', $memberNumber)->exists();
            if ($exists) {
                $newNumber++;
            }
        } while ($exists);
        
        return $memberNumber;
    }
}