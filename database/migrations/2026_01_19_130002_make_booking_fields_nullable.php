<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('bill_code')->nullable()->change();
            $table->string('transaction_reference_no')->nullable()->change();
            $table->dateTime('transaction_time')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Set default values for NULL entries before making columns NOT NULL
        DB::table('bookings')->whereNull('bill_code')->update(['bill_code' => '']);
        DB::table('bookings')->whereNull('transaction_reference_no')->update(['transaction_reference_no' => '']);
        DB::table('bookings')->whereNull('transaction_time')->update(['transaction_time' => now()]);

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('bill_code')->nullable(false)->change();
            $table->string('transaction_reference_no')->nullable(false)->change();
            $table->dateTime('transaction_time')->nullable(false)->change();
        });
    }
};
