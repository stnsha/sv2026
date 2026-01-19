<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckAvailabilityRequest;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Date;
use App\Models\Price;
use App\Models\TimeSlot;
use App\Services\BookingService;
use App\Services\ToyyibPayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        private BookingService $bookingService,
        private ToyyibPayService $toyyibPayService
    ) {}

    public function index(): View
    {
        $dates = Date::orderBy('date_value')->get();
        $timeSlots = TimeSlot::all();
        $prices = Price::all();

        return view('bookings.index', compact('dates', 'timeSlots', 'prices'));
    }

    public function checkAvailability(CheckAvailabilityRequest $request): JsonResponse
    {
        $result = $this->bookingService->checkAvailability(
            $request->input('date_id'),
            $request->input('time_slot_id'),
            $request->input('total_pax')
        );

        return response()->json($result);
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        try {
            $booking = $this->bookingService->createBooking(
                $request->input('customer'),
                $request->input('date_id'),
                $request->input('time_slot_id'),
                $request->input('pax_details')
            );

            $billResult = $this->toyyibPayService->createBill($booking);

            if (!$billResult['success']) {
                $this->bookingService->handlePaymentFailure($booking, $billResult['error']);

                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Unable to initiate payment. Please try again.');
            }

            $this->bookingService->initiatePayment($booking, $billResult['bill_code']);

            $paymentUrl = $this->toyyibPayService->getPaymentUrl($billResult['bill_code']);

            return redirect()->away($paymentUrl);
        } catch (\RuntimeException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(Booking $booking): View
    {
        $booking->load(['customer', 'date', 'timeSlot', 'details.price', 'tableBookings.table']);

        return view('bookings.show', compact('booking'));
    }
}
