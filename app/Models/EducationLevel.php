<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationLevel extends Model
{
    protected $fillable = [
        'name',
        'code',
        'order',
        'is_active',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Scope for active education levels.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope ordered by display order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
