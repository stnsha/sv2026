<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    protected $fillable = [
        'table_number',
        'seat_type',
        'capacity',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    public function tableBookings(): HasMany
    {
        return $this->hasMany(TableBooking::class);
    }
}
