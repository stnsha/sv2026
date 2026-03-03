<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prices', function (Blueprint $table) {
            $table->dropColumn(['is_adult', 'min_adult_pax']);
        });
    }

    public function down(): void
    {
        Schema::table('prices', function (Blueprint $table) {
            $table->boolean('is_adult')->default(false)->after('is_active');
            $table->unsignedTinyInteger('min_adult_pax')->nullable()->after('is_adult');
        });
    }
};
