<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Cabor extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'federation',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cabor) {
            if (empty($cabor->slug)) {
                $cabor->slug = Str::slug($cabor->name);
            }
        });
    }

    /**
     * Get athletes in this cabor.
     */
    public function athletes(): HasMany
    {
        return $this->hasMany(Athlete::class);
    }

    /**
     * Get active athletes count.
     */
    public function getActiveAthletesCountAttribute(): int
    {
        return $this->athletes()->where('is_active', true)->count();
    }
}
