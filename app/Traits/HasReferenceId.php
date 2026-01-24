<?php

namespace App\Traits;

use App\Models\Date;
use Carbon\Carbon;

trait HasReferenceId
{
    private const REFERENCE_CHARACTERS = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

    public static function generateUniqueReferenceId(int $dateId, int $timeSlotId, int $suffixLength = 3): string
    {
        $date = Date::findOrFail($dateId);
        $dateValue = $date->date_value instanceof Carbon
            ? $date->date_value
            : Carbon::parse($date->date_value);

        $datePrefix = $dateValue->format('ymd');
        $prefix = $datePrefix . $timeSlotId;

        do {
            $suffix = '';
            for ($i = 0; $i < $suffixLength; $i++) {
                $suffix .= self::REFERENCE_CHARACTERS[random_int(0, strlen(self::REFERENCE_CHARACTERS) - 1)];
            }
            $referenceId = $prefix . $suffix;
        } while (static::where('reference_id', $referenceId)->exists());

        return $referenceId;
    }
}
