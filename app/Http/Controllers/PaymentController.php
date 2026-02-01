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
        $data = $this->toyyibPayService->parseCallback($request->all());

        Log::info('ToyyibPay callback received', $data);

        if (empty($data['bill_code'])) {
            Log::warning('ToyyibPay callback missing bill code', $request->all());
            return response('Missing bill code', 400);
        }

        $booking = Booking::where('bill_code', $data['bill_code'])->first();

        if (!$booking) {
            Log::error('Booking not found for callback', ['bill_code' => $data['bill_code']]);
            return response('Booking not found', 404);
        }

        if ($data['is_paid']) {
            $this->bookingService->confirmBooking(
                $booking,
                $data['transaction_id'],
                new DateTime()
            );

            Log::info('Payment successful', [
                'booking_id' => $booking->id,
                'transaction_id' => $data['transaction_id'],
            ]);
        } elseif ($data['is_pending']) {
            $booking->update([
                'status_message' => 'Pending: ' . ($data['reason'] ?? 'Awaiting payment confirmation'),
            ]);

            Log::info('Payment pending', [
                'booking_id' => $booking->id,
                'reason' => $data['reason'],
            ]);
        } else {
            $this->bookingService->handlePaymentFailure(
                $booking,
                'Failed: ' . ($data['reason'] ?? 'Unknown')
            );

            Log::info('Payment failed', [
                'booking_id' => $booking->id,
                'reason' => $data['reason'],
            ]);
        }

        return response('OK', 200);
    }

    public function redirect(Request $request): RedirectResponse
    {
        $data = $this->toyyibPayService->parseRedirect($request->all());

        Log::info('ToyyibPay redirect received', $data);

        $booking = Booking::where('bill_code', $data['bill_code'])->first();

        if (!$booking) {
            return redirect()->route('booking.index')
                ->with('error', 'Booking not found.');
        }

        // Update booking status if not already updated by callback
        // Matches buffet26: if ($order->status === 0)
        if ($booking->status === Booking::STATUS_PENDING_PAYMENT) {
            if ($data['is_paid']) {
                $this->bookingService->confirmBooking(
                    $booking,
                    $data['transaction_id'] ?? '',
                    new DateTime()
                );
            } elseif ($data['is_pending']) {
                $booking->update([
                    'status_message' => 'Pending: ' . ($data['reason'] ?? 'Awaiting payment confirmation'),
                ]);
            } else {
                // is_failed (status_id = 3)
                $this->bookingService->handlePaymentFailure(
                    $booking,
                    'Failed: ' . ($data['reason'] ?? 'Unknown')
                );
            }
        }

        $booking->refresh();

        if ($data['is_paid']) {
            return redirect()->route('booking.show', $booking)
                ->with('success', 'Payment successful! Your booking is confirmed.');
        }

        if ($data['is_pending']) {
            return redirect()->route('booking.show', $booking)
                ->with('warning', 'Payment is pending. Please wait for confirmation.');
        }

        return redirect()->route('booking.show', $booking)
            ->with('error', 'Payment was not successful. Please try again.');
    }
}
