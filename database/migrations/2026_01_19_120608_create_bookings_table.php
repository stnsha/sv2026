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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount', 10, 2);
            $table->decimal('service_charge', 10, 2); // e.g., 1.00
            $table->decimal('total', 10, 2);
            $table->string('bill_code');
            $table->unsignedTinyInteger('status')->default(0); // 0:initiated
            $table->string('status_message')->nullable();
            $table->string('transaction_reference_no');
            $table->dateTime('transaction_time');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
