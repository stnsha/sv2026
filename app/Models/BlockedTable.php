<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockedTable extends Model
{
    protected $fillable = [
        'table_id',
        'date_id',
        'time_slot_id',
        'reason',
    ];

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function date(): BelongsTo
    {
        return $this->belongsTo(Date::class);
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }
}
