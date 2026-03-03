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
            $table->boolean('is_active')->default(true)->after('extra_chair');
            $table->boolean('is_adult')->default(false)->after('is_active');
            $table->unsignedTinyInteger('min_adult_pax')->nullable()->after('is_adult');
        });

        DB::table('prices')
            ->where('category', 'Dewasa')
            ->update(['is_adult' => true]);
    }

    public function down(): void
    {
        Schema::table('prices', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'is_adult', 'min_adult_pax']);
        });
    }
};
