<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Price extends Model
{
    protected $fillable = [
        'category',
        'amount',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function bookingDetails(): HasMany
    {
        return $this->hasMany(BookingDetails::class);
    }
}
