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

    protected $description = 'Clean up expired initiated and pending payment bookings';

    public function handle(BookingService $bookingService, ToyyibPayService $toyyibPayService): int
    {
        Log::info('bookings:cleanup-expired started');

        $this->cleanupInitiatedBookings($bookingService);
        $this->cleanupPendingPaymentBookings($bookingService, $toyyibPayService);

        Log::info('bookings:cleanup-expired completed');

        return self::SUCCESS;
    }

    private function cleanupInitiatedBookings(BookingService $bookingService): void
    {
        $initiatedBookings = Booking::query()
            ->where('status', Booking::STATUS_INITIATED)
            ->where('created_at', '<', now()->subMinutes(10))
            ->get();

        if ($initiatedBookings->isEmpty()) {
            Log::debug('No expired initiated bookings found');
            $this->info('No expired initiated bookings found.');

            return;
        }

        Log::info('Found expired initiated bookings', ['count' => $initiatedBookings->count()]);
        $this->info("Found {$initiatedBookings->count()} expired initiated booking(s).");

        foreach ($initiatedBookings as $booking) {
            $bookingService->handlePaymentFailure($booking, 'Booking expired - never proceeded to payment');

            Log::info('Expired initiated booking cleaned up', [
                'booking_id' => $booking->id,
                'reference_id' => $booking->reference_id,
            ]);

            $this->info("Initiated booking {$booking->reference_id} expired and cleaned up.");
        }
    }

    private function cleanupPendingPaymentBookings(
        BookingService $bookingService,
        ToyyibPayService $toyyibPayService,
    ): void {
        $expiredBookings = Booking::query()
            ->where('status', Booking::STATUS_PENDING_PAYMENT)
            ->whereNotNull('bill_code')
            ->where('updated_at', '<', now()->subMinutes(5))
            ->get();

        if ($expiredBookings->isEmpty()) {
            Log::debug('No expired pending payment bookings found');
            $this->info('No expired pending payment bookings found.');

            return;
        }

        Log::info('Found expired pending payment bookings', ['count' => $expiredBookings->count()]);
        $this->info("Found {$expiredBookings->count()} expired pending payment booking(s).");

        foreach ($expiredBookings as $booking) {
            $this->processExpiredBooking($booking, $bookingService, $toyyibPayService);
        }
    }

    private function processExpiredBooking(
        Booking $booking,
        BookingService $bookingService,
        ToyyibPayService $toyyibPayService,
    ): void {
        $transactions = $toyyibPayService->getBillTransactions($booking->bill_code, 1);

        if ($transactions['success'] && !empty($transactions['data'])) {
            Log::info('Expired booking was actually paid, confirming', [
                'booking_id' => $booking->id,
                'reference_id' => $booking->reference_id,
                'bill_code' => $booking->bill_code,
                'invoice_no' => $transactions['data'][0]['billpaymentInvoiceNo'] ?? '',
            ]);
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
