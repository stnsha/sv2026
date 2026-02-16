<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MinimumPaxOverride extends Model
{
    protected $fillable = [
        'date_id',
        'time_slot_id',
        'minimum_pax',
    ];

    protected $casts = [
        'minimum_pax' => 'integer',
    ];

    public function date(): BelongsTo
    {
        return $this->belongsTo(Date::class);
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }
}
