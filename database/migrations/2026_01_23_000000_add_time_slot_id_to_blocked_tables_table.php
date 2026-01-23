<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Delete existing records - old records have no slot info
        DB::table('blocked_tables')->truncate();

        Schema::table('blocked_tables', function (Blueprint $table) {
            // Drop foreign key on table_id first (MySQL uses unique index for FK)
            $table->dropForeign(['table_id']);

            // Drop existing unique constraint
            $table->dropUnique(['table_id', 'date_id']);
        });

        Schema::table('blocked_tables', function (Blueprint $table) {
            // Add time_slot_id column
            $table->foreignId('time_slot_id')
                ->after('date_id')
                ->constrained('time_slots')
                ->cascadeOnDelete();

            // Re-add foreign key on table_id
            $table->foreign('table_id')
                ->references('id')
                ->on('tables')
                ->cascadeOnDelete();

            // Add new unique constraint
            $table->unique(['table_id', 'date_id', 'time_slot_id']);
        });
    }

    public function down(): void
    {
        Schema::table('blocked_tables', function (Blueprint $table) {
            // Drop foreign keys first (MySQL requires this before dropping unique index)
            $table->dropForeign(['table_id']);
            $table->dropForeign(['time_slot_id']);

            // Drop unique constraint
            $table->dropUnique(['table_id', 'date_id', 'time_slot_id']);

            // Drop time_slot_id column
            $table->dropColumn('time_slot_id');
        });

        Schema::table('blocked_tables', function (Blueprint $table) {
            // Restore original unique constraint
            $table->unique(['table_id', 'date_id']);

            // Re-add foreign key on table_id
            $table->foreign('table_id')
                ->references('id')
                ->on('tables')
                ->cascadeOnDelete();
        });
    }
};
