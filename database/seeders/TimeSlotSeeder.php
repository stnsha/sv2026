<?php

namespace Database\Seeders;

use App\Models\TimeSlot;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimeSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'start_time' => '19:00:00',
                'end_time' => '21:15:00',
            ],
            [
                'start_time' => '21:15:00',
                'end_time' => '23:00:00',
            ],
        ];

        foreach ($data as $item) {
            TimeSlot::firstOrCreate(
                [
                    'start_time' => $item['start_time'],
                    'end_time' => $item['end_time'],
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
