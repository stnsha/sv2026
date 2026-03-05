<?php

namespace App\Console\Commands;

use App\Mail\BookingInvoice;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ResendBookingInvoices extends Command
{
    protected $signature = 'bookings:resend-invoices {--from=2026-03-06 : Start date (Y-m-d)}';

    protected $description = 'Resend invoice emails to all confirmed bookings from the given date onwards';

    public function handle(): int
    {
        $from = $this->option('from');

        $bookings = Booking::query()
            ->with(['customer', 'date', 'timeSlot', 'details.price', 'tableBookings.table'])
            ->where('status', Booking::STATUS_CONFIRMED)
            ->whereHas('date', fn ($q) => $q->where('date_value', '>=', $from))
            ->get();

        if ($bookings->isEmpty()) {
            $this->info("No confirmed bookings found from {$from} onwards.");

            return self::SUCCESS;
        }

        $this->info("Found {$bookings->count()} confirmed booking(s) from {$from} onwards.");

        $sent = 0;
        $failed = 0;

        foreach ($bookings as $booking) {
            try {
                Mail::to($booking->customer->email)->send(new BookingInvoice($booking));
                $this->info("Sent: {$booking->reference_id} → {$booking->customer->email}");
                $sent++;
            } catch (\Throwable $e) {
                $this->error("Failed: {$booking->reference_id} — {$e->getMessage()}");
                $failed++;
            }
        }

        $this->info("Done. Sent: {$sent}, Failed: {$failed}.");

        return self::SUCCESS;
    }
}
