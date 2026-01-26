<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompetitionClass extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'cabor_id',
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the cabor that owns this competition class.
     */
    public function cabor(): BelongsTo
    {
        return $this->belongsTo(Cabor::class);
    }

    /**
     * Get athletes in this competition class.
     */
    public function athletes(): HasMany
    {
        return $this->hasMany(Athlete::class);
    }
}
