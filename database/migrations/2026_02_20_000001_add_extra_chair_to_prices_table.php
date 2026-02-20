<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prices', function (Blueprint $table) {
            $table->boolean('extra_chair')->default(false)->after('description');
        });

        DB::table('prices')
            ->where('category', 'Kanak-kanak')
            ->update(['description' => '5 tahun dan ke atas']);

        DB::table('prices')->insert([
            'category' => 'Kanak-kanak',
            'description' => '4 tahun dan ke bawah',
            'amount' => 10.00,
            'extra_chair' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('prices')
            ->where('category', 'Kanak-kanak')
            ->where('description', '4 tahun dan ke bawah')
            ->delete();

        DB::table('prices')
            ->where('category', 'Kanak-kanak')
            ->where('description', '5 tahun dan ke atas')
            ->update(['description' => 'Children pricing']);

        Schema::table('prices', function (Blueprint $table) {
            $table->dropColumn('extra_chair');
        });
    }
};
