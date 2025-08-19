<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShareBonus extends Model
{
    protected $fillable = [
        'title',
        'amount',
        'date',
        'description',
        'status',
        'branch_id',
        'recipient_id',
        'created_by',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'approved_at' => 'datetime'
    ];

    /**
     * Get the branch that owns the share bonus
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the user who created the share bonus
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved the share bonus
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the member who receives the share bonus
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'recipient_id');
    }

    /**
     * Scope for approved share bonuses
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for pending share bonuses
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
