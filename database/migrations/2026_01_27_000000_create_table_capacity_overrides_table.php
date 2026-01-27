<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('table_capacity_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_id')->constrained('tables')->cascadeOnDelete();
            $table->foreignId('date_id')->constrained('dates')->cascadeOnDelete();
            $table->foreignId('time_slot_id')->constrained('time_slots')->cascadeOnDelete();
            $table->unsignedInteger('effective_capacity');
            $table->timestamps();
            $table->unique(['table_id', 'date_id', 'time_slot_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('table_capacity_overrides');
    }
};
