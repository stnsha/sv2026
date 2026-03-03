<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Price extends Model
{
    protected $fillable = [
        'category',
        'amount',
        'description',
        'extra_chair',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'extra_chair' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function bookingDetails(): HasMany
    {
        return $this->hasMany(BookingDetails::class);
    }
}
