<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'role_id',
        'branch_id',
        'employee_id',
        'phone',
        'status',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
        ];
    }

    // Relationships
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
    
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function approvedLoans(): HasMany
    {
        return $this->hasMany(Loan::class, 'approved_by');
    }

    public function processedTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'processed_by');
    }

    public function requestedExpenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'requested_by');
    }

    public function approvedExpenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'approved_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByRole($query, $roleSlug)
    {
        return $query->whereHas('role', function($q) use ($roleSlug) {
            $q->where('slug', $roleSlug);
        });
    }

    // Methods
    public function isSuperAdmin(): bool
    {
        return $this->role()->exists() && $this->role->slug === 'super-admin';
    }
    
    public function hasPermission($permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        
        return $this->role()->exists() && $this->role->hasPermission($permission);
    }
    
    public function can($abilities, $arguments = [])
    {
        // If $abilities is an array/iterable, check if user has any of the abilities
        if (is_iterable($abilities)) {
            foreach ($abilities as $ability) {
                if ($this->hasPermission($ability)) {
                    return true;
                }
            }
            return false;
        }
        
        // Otherwise, treat it as a single permission check
        return $this->hasPermission($abilities);
    }

    public function isBranchManager(): bool
    {
        return $this->role()->exists() && $this->role->slug === 'branch-manager';
    }

    public function isFieldOfficer(): bool
    {
        return $this->role()->exists() && $this->role->slug === 'field-officer';
    }

    public function isAccountant(): bool
    {
        return $this->role()->exists() && $this->role->slug === 'accountant';
    }

    public function isMember(): bool
    {
        return $this->role()->exists() && $this->role->slug === 'member';
    }
}
