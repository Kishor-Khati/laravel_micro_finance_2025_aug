<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'slug',
        'description',
        'permissions',
    ];
    
    protected $casts = [
        'permissions' => 'array',
    ];
    
    // Relationships
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
    
    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
    // Methods
    public function hasPermission($permission): bool
    {
        if (!$this->permissions) {
            return false;
        }
        
        // Ensure permissions is an array
        $permissions = is_array($this->permissions) ? $this->permissions : json_decode($this->permissions, true);
        
        return in_array($permission, $permissions ?? []);
    }
}
