<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\BookingService;
use App\Services\ToyyibPayService;
use DateTime;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentController extends Controller
{
    public function __construct(
        private BookingService $bookingService,
        private ToyyibPayService $toyyibPayService
    ) {}

    public function callback(Request $request): Response
    {
        $result = $this->toyyibPayService->handleCallback($request->all());

        if (!$result['success']) {
            return response($result['error'], 400);
        }

        $booking = $result['booking'];

        if ($result['is_paid']) {
            $this->bookingService->confirmBooking(
                $booking,
                $result['transaction_id'],
                new DateTime()
            );
        } else {
            $this->bookingService->handlePaymentFailure(
                $booking,
                $result['reason'] ?? 'Payment was not successful'
            );
        }

        return response('OK', 200);
    }

    public function redirect(Request $request): RedirectResponse
    {
        $billCode = $request->input('billcode');
        $status = $request->input('status_id');

        $booking = Booking::where('bill_code', $billCode)->first();

        if (!$booking) {
            return redirect()->route('booking.index')
                ->with('error', 'Booking not found.');
        }

        if ($status === '1') {
            return redirect()->route('booking.show', $booking)
                ->with('success', 'Payment successful! Your booking is confirmed.');
        }

        return redirect()->route('booking.show', $booking)
            ->with('error', 'Payment was not successful. Please try again.');
    }
}
