<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\BookingService;
use App\Services\ToyyibPayService;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupExpiredBookings extends Command
{
    protected $signature = 'bookings:cleanup-expired';

    protected $description = 'Clean up bookings that have been pending payment for more than 5 minutes';

    public function handle(BookingService $bookingService, ToyyibPayService $toyyibPayService): int
    {
        Log::info('bookings:cleanup-expired started');

        $expiredBookings = Booking::query()
            ->where('status', Booking::STATUS_PENDING_PAYMENT)
            ->whereNotNull('bill_code')
            ->where('updated_at', '<', now()->subMinutes(5))
            ->get();

        if ($expiredBookings->isEmpty()) {
            $this->info('No expired bookings found.');

            return self::SUCCESS;
        }

        $this->info("Found {$expiredBookings->count()} expired booking(s).");

        foreach ($expiredBookings as $booking) {
            $this->processExpiredBooking($booking, $bookingService, $toyyibPayService);
        }

        return self::SUCCESS;
    }

    private function processExpiredBooking(
        Booking $booking,
        BookingService $bookingService,
        ToyyibPayService $toyyibPayService,
    ): void {
        $transactions = $toyyibPayService->getBillTransactions($booking->bill_code, 1);

        if ($transactions['success'] && !empty($transactions['data'])) {
            $this->info("Booking {$booking->reference_id} was actually paid. Confirming.");

            $bookingService->confirmBooking(
                $booking,
                $transactions['data'][0]['billpaymentInvoiceNo'] ?? '',
                new DateTime(),
            );

            return;
        }

        $toyyibPayService->inactiveBill($booking->bill_code);

        $bookingService->handlePaymentFailure($booking, 'Payment expired');

        Log::info('Expired booking cleaned up', [
            'booking_id' => $booking->id,
            'reference_id' => $booking->reference_id,
            'bill_code' => $booking->bill_code,
        ]);

        $this->info("Booking {$booking->reference_id} expired and cleaned up.");
    }
}
