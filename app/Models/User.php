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

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // Methods
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isBranchManager(): bool
    {
        return $this->role === 'branch_manager';
    }

    public function isFieldOfficer(): bool
    {
        return $this->role === 'field_officer';
    }

    public function isAccountant(): bool
    {
        return $this->role === 'accountant';
    }

    public function isMember(): bool
    {
        return $this->role === 'member';
    }
}
