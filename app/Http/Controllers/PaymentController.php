<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\BookingService;
use App\Services\ToyyibPayService;
use DateTime;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        private BookingService $bookingService,
        private ToyyibPayService $toyyibPayService
    ) {}

    public function callback(Request $request): Response
    {
        $parsed = $this->toyyibPayService->parseCallback($request->all());

        if (empty($parsed['bill_code'])) {
            Log::warning('ToyyibPay callback missing bill code', $request->all());

            return response('Missing bill code', 400);
        }

        $booking = Booking::where('bill_code', $parsed['bill_code'])->first();

        if (!$booking) {
            Log::warning('ToyyibPay callback booking not found', ['bill_code' => $parsed['bill_code']]);

            return response('Booking not found', 404);
        }

        if ($parsed['is_paid']) {
            $this->bookingService->confirmBooking(
                $booking,
                $parsed['transaction_id'],
                new DateTime()
            );
        } else {
            $this->bookingService->handlePaymentFailure(
                $booking,
                $parsed['reason'] ?? 'Payment was not successful'
            );
        }

        return response('OK', 200);
    }

    public function redirect(Request $request): RedirectResponse
    {
        $parsed = $this->toyyibPayService->parseRedirect($request->all());

        $booking = Booking::where('bill_code', $parsed['bill_code'])->first();

        if (!$booking) {
            return redirect()->route('booking.index')
                ->with('error', 'Booking not found.');
        }

        if ($parsed['is_paid']) {
            if ($booking->status === Booking::STATUS_PENDING_PAYMENT) {
                $transactions = $this->toyyibPayService->getBillTransactions($parsed['bill_code'], 1);

                if ($transactions['success'] && !empty($transactions['data'])) {
                    $this->bookingService->confirmBooking(
                        $booking,
                        $transactions['data'][0]['billpaymentInvoiceNo'] ?? '',
                        new DateTime()
                    );
                }
            }

            return redirect()->route('booking.show', $booking->fresh())
                ->with('success', 'Payment successful! Your booking is confirmed.');
        }

        $reason = $parsed['reason'] ?? 'Payment was not successful';

        if ($booking->status === Booking::STATUS_PENDING_PAYMENT) {
            $this->bookingService->handlePaymentFailure($booking, $reason);
        } elseif ($booking->status === Booking::STATUS_PAYMENT_FAILED && $reason) {
            $booking->update(['status_message' => $reason]);
        }

        return redirect()->route('booking.show', $booking->fresh())
            ->with('error', 'Payment was not successful. Please try again.');
    }
}
