<?php

namespace Database\Seeders;

use App\Models\Price;
use Illuminate\Database\Seeder;

class PriceSeeder extends Seeder
{
    public function run(): void
    {
        Price::firstOrCreate(
            ['category' => 'Dewasa'],
            ['amount' => 55.90, 'description' => 'Adult pricing', 'extra_chair' => false]
        );

        Price::firstOrCreate(
            ['category' => 'Warga Emas'],
            ['amount' => 48.90, 'description' => 'Senior citizen pricing', 'extra_chair' => false]
        );

        Price::updateOrCreate(
            ['category' => 'Kanak-kanak', 'description' => '5 tahun dan ke atas'],
            ['amount' => 39.90, 'extra_chair' => false]
        );

        Price::firstOrCreate(
            ['category' => 'Kanak-kanak', 'description' => '4 tahun dan ke bawah'],
            ['amount' => 10.00, 'extra_chair' => true]
        );
    }
}
