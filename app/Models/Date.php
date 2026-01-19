<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Date extends Model
{
    protected $fillable = [
        'date_value',
    ];

    protected $casts = [
        'date_value' => 'date',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function tableBookings(): HasMany
    {
        return $this->hasMany(TableBooking::class);
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->date_value->format('d M Y');
    }
}
