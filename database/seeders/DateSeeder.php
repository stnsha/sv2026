<?php

namespace Database\Seeders;

use App\Models\Date;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $startDate = Carbon::create(2026, 2, 21);
        $endDate   = Carbon::create(2026, 3, 17);

        while ($startDate->lte($endDate)) {
            Date::firstOrCreate(
                ['date_value' => $startDate->toDateString()],
                ['created_at' => now(), 'updated_at' => now()]
            );

            $startDate->addDay();
        }
    }
}
