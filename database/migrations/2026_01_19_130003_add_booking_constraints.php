<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('date_id')->references('id')->on('dates')->onDelete('cascade');
            $table->foreign('time_slot_id')->references('id')->on('time_slots')->onDelete('cascade');
        });

        Schema::table('booking_details', function (Blueprint $table) {
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->foreign('price_id')->references('id')->on('prices')->onDelete('cascade');
        });

        Schema::table('table_bookings', function (Blueprint $table) {
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->foreign('date_id')->references('id')->on('dates')->onDelete('cascade');
            $table->foreign('time_slot_id')->references('id')->on('time_slots')->onDelete('cascade');
            $table->foreign('table_id')->references('id')->on('tables')->onDelete('cascade');

            $table->unique(['date_id', 'time_slot_id', 'table_id'], 'unique_table_slot_date');
        });
    }

    public function down(): void
    {
        Schema::table('table_bookings', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);
            $table->dropForeign(['date_id']);
            $table->dropForeign(['time_slot_id']);
            $table->dropForeign(['table_id']);
            $table->dropUnique('unique_table_slot_date');
        });

        Schema::table('booking_details', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);
            $table->dropForeign(['price_id']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['date_id']);
            $table->dropForeign(['time_slot_id']);
        });
    }
};
