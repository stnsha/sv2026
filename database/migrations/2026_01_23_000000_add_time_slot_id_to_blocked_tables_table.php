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

        // Drop constraints that may exist
        $this->dropConstraintIfExists('blocked_tables', 'blocked_tables_table_id_foreign', 'foreign');
        $this->dropConstraintIfExists('blocked_tables', 'blocked_tables_table_id_date_id_unique', 'unique');

        Schema::table('blocked_tables', function (Blueprint $table) {
            // Add time_slot_id column if not exists
            if (!Schema::hasColumn('blocked_tables', 'time_slot_id')) {
                $table->foreignId('time_slot_id')
                    ->after('date_id')
                    ->constrained('time_slots')
                    ->cascadeOnDelete();
            }

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
        // Truncate to avoid duplicate constraint violations when restoring old unique key
        DB::table('blocked_tables')->truncate();

        // Drop constraints that may exist
        $this->dropConstraintIfExists('blocked_tables', 'blocked_tables_table_id_foreign', 'foreign');
        $this->dropConstraintIfExists('blocked_tables', 'blocked_tables_time_slot_id_foreign', 'foreign');
        $this->dropConstraintIfExists('blocked_tables', 'blocked_tables_table_id_date_id_time_slot_id_unique', 'unique');

        Schema::table('blocked_tables', function (Blueprint $table) {
            // Drop time_slot_id column if exists
            if (Schema::hasColumn('blocked_tables', 'time_slot_id')) {
                $table->dropColumn('time_slot_id');
            }

            // Restore original unique constraint
            $table->unique(['table_id', 'date_id']);

            // Re-add foreign key on table_id
            $table->foreign('table_id')
                ->references('id')
                ->on('tables')
                ->cascadeOnDelete();
        });
    }

    private function dropConstraintIfExists(string $table, string $name, string $type): void
    {
        $exists = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = ?
            AND CONSTRAINT_NAME = ?
        ", [$table, $name]);

        if (!empty($exists)) {
            if ($type === 'foreign') {
                Schema::table($table, fn (Blueprint $t) => $t->dropForeign($name));
            } else {
                Schema::table($table, fn (Blueprint $t) => $t->dropUnique($name));
            }
        }
    }
};
