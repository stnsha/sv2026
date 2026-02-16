<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('minimum_pax_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('date_id')->constrained('dates')->cascadeOnDelete();
            $table->foreignId('time_slot_id')->constrained('time_slots')->cascadeOnDelete();
            $table->unsignedTinyInteger('minimum_pax');
            $table->timestamps();

            $table->unique(['date_id', 'time_slot_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('minimum_pax_overrides');
    }
};
