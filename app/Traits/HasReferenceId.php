<?php

namespace App\Traits;

trait HasReferenceId
{
    private const REFERENCE_CHARACTERS = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

    public static function generateUniqueReferenceId(int $length = 5): string
    {
        do {
            $referenceId = '';
            for ($i = 0; $i < $length; $i++) {
                $referenceId .= self::REFERENCE_CHARACTERS[random_int(0, strlen(self::REFERENCE_CHARACTERS) - 1)];
            }
        } while (static::where('reference_id', $referenceId)->exists());

        return $referenceId;
    }
}
