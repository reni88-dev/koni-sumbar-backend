<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormFieldOption extends Model
{
    use LogsActivity;
    protected $fillable = [
        'form_field_id',
        'label',
        'value',
        'order',
    ];

    /**
     * Get the field this option belongs to.
     */
    public function field(): BelongsTo
    {
        return $this->belongsTo(FormField::class, 'form_field_id');
    }
}
