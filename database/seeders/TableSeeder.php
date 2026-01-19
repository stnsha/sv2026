<?php

namespace Database\Seeders;

use App\Models\Table;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        // 20 x 6-seater tables (T601-T620)
        for ($i = 1; $i <= 20; $i++) {
            Table::firstOrCreate(
                ['table_number' => sprintf('T6%02d', $i)],
                [
                    'seat_type' => '6-seater',
                    'capacity' => 6,
                ]
            );
        }

        // 18 x 4-seater tables (T401-T418)
        for ($i = 1; $i <= 18; $i++) {
            Table::firstOrCreate(
                ['table_number' => sprintf('T4%02d', $i)],
                [
                    'seat_type' => '4-seater',
                    'capacity' => 4,
                ]
            );
        }
    }
}
