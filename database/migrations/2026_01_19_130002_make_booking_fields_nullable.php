<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('bill_code')->nullable(false)->change();
            $table->string('transaction_reference_no')->nullable(false)->change();
            $table->dateTime('transaction_time')->nullable(false)->change();
        });
    }
};
