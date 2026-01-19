<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    public const STATUS_INITIATED = 0;
    public const STATUS_PENDING_PAYMENT = 1;
    public const STATUS_CONFIRMED = 2;
    public const STATUS_CANCELLED = 3;
    public const STATUS_PAYMENT_FAILED = 4;

    protected $fillable = [
        'customer_id',
        'date_id',
        'time_slot_id',
        'subtotal',
        'discount',
        'service_charge',
        'total',
        'bill_code',
        'status',
        'status_message',
        'transaction_reference_no',
        'transaction_time',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'total' => 'decimal:2',
        'status' => 'integer',
        'transaction_time' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function date(): BelongsTo
    {
        return $this->belongsTo(Date::class);
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(BookingDetails::class);
    }

    public function tableBookings(): HasMany
    {
        return $this->hasMany(TableBooking::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_INITIATED => 'Initiated',
            self::STATUS_PENDING_PAYMENT => 'Pending Payment',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_PAYMENT_FAILED => 'Payment Failed',
            default => 'Unknown',
        };
    }
}
