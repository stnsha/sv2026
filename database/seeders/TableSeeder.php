<?php

namespace Database\Seeders;

use App\Models\Table;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        $t6Tables = [
            'T6/1', 'T6/2', 'T6/3', 'T6/4', 'T6/5', 'T6/6', 'T6/7', 'T6/8',
            'T6/10', 'T6/11', 'T6/12', 'T6/14', 'T6/15', 'T6/17', 'T6/18',
            'T6/33', 'T6/34', 'T6/35', 'T6/36', 'T6/37',
        ];

        foreach ($t6Tables as $tableNumber) {
            Table::firstOrCreate(
                ['table_number' => $tableNumber],
                [
                    'seat_type' => '6-seater',
                    'capacity' => 6,
                ]
            );
        }

        $t4Tables = [
            'T4/13', 'T4/16', 'T4/19', 'T4/20', 'T4/21', 'T4/22', 'T4/23',
            'T4/24', 'T4/25', 'T4/26', 'T4/27', 'T4/28', 'T4/29', 'T4/30',
            'T4/31', 'T4/32',
        ];

        foreach ($t4Tables as $tableNumber) {
            Table::firstOrCreate(
                ['table_number' => $tableNumber],
                [
                    'seat_type' => '4-seater',
                    'capacity' => 4,
                ]
            );
        }
    }
}
