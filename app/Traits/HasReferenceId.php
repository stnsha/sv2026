<?php

namespace App\Traits;

use App\Models\Date;
use Carbon\Carbon;

trait HasReferenceId
{
    private const REFERENCE_LETTERS = 'ABCDEFGHJKMNPQRSTUVWXYZ';

    public static function generateReferenceId(int $bookingId, int $dateId): string
    {
        $date = Date::findOrFail($dateId);
        $dateValue = $date->date_value instanceof Carbon
            ? $date->date_value
            : Carbon::parse($date->date_value);

        $letters = '';
        for ($i = 0; $i < 2; $i++) {
            $letters .= self::REFERENCE_LETTERS[random_int(0, strlen(self::REFERENCE_LETTERS) - 1)];
        }

        $day = $dateValue->day;
        $month = $dateValue->month;

        return 'SV' . $bookingId . $letters . $day . $month;
    }
}
