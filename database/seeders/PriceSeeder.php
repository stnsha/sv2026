<?php

namespace Database\Seeders;

use App\Models\Price;
use Illuminate\Database\Seeder;

class PriceSeeder extends Seeder
{
    public function run(): void
    {
        $prices = [
            [
                'category' => 'Dewasa',
                'amount' => 55.90,
                'description' => 'Adult pricing',
            ],
            [
                'category' => 'Warga Emas',
                'amount' => 48.90,
                'description' => 'Senior citizen pricing',
            ],
            [
                'category' => 'Kanak-kanak',
                'amount' => 39.90,
                'description' => 'Children pricing',
            ],
        ];

        foreach ($prices as $price) {
            Price::firstOrCreate(
                ['category' => $price['category']],
                [
                    'amount' => $price['amount'],
                    'description' => $price['description'],
                ]
            );
        }
    }
}
