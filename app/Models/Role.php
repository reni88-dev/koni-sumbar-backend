<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use LogsActivity;
    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    /**
     * Get users with this role.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get permissions for this role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    /**
     * Check if role has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->where('name', $permission)->exists();
    }

    /**
     * Check if this is the super admin role.
     */
    public function isSuperAdmin(): bool
    {
        return $this->name === 'super_admin';
    }

    /**
     * Check if this is the admin role.
     */
    public function isAdmin(): bool
    {
        return $this->name === 'admin';
    }

    /**
     * Sync permissions to this role.
     */
    public function syncPermissions(array $permissionIds): void
    {
        $this->permissions()->sync($permissionIds);
    }
}
