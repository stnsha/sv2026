<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('booking_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger(column: 'price_id');
            $table->integer('quantity'); //e.g., 2 Dewasa
            $table->decimal('subtotal', 8, 2); //e.g., 199.98
            $table->decimal('discount', 8, 2)->default(0); //e.g., 0.00
            $table->decimal('total', 8, 2); //e.g., 199.98
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_details');
    }
};
