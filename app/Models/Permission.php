<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use LogsActivity;
    protected $fillable = [
        'name',
        'display_name',
        'group',
        'description',
    ];

    /**
     * Get roles that have this permission.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }
}
