<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimeSlot extends Model
{
    protected $fillable = [
        'start_time',
        'end_time',
        'minimum_pax',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'minimum_pax' => 'integer',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function tableBookings(): HasMany
    {
        return $this->hasMany(TableBooking::class);
    }

    public function minimumPaxOverrides(): HasMany
    {
        return $this->hasMany(MinimumPaxOverride::class);
    }

    public function getFormattedTimeAttribute(): string
    {
        return $this->start_time->format('g:i A') . ' - ' . $this->end_time->format('g:i A');
    }
}
