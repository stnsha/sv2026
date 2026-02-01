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
use RuntimeException;

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

            $customer = $booking->customer;

            $billData = [
                'billName' => 'Booking #' . $booking->reference_id,
                'billDescription' => 'Sand Village booking for ' . $booking->date->formatted_date,
                'billPriceSetting' => 1,
                'billPayorInfo' => 1,
                'billAmount' => (int) round($booking->total * 100),
                'billReturnUrl' => route('payment.redirect'),
                'billCallbackUrl' => route('payment.callback'),
                'billExternalReferenceNo' => $booking->reference_id,
                'billTo' => $customer->name,
                'billEmail' => $customer->email,
                'billPhone' => $customer->phone_number,
                'billSplitPayment' => 0,
                'billSplitPaymentArgs' => '',
                'billContentEmail' => 'Thank you for your booking. Your payment has been received.',
                'billExpiryDate' => now('Asia/Kuala_Lumpur')->addMinutes(5)->format('d-m-Y H:i:s'),
                'billExpiryDays' => 0,
                'billChargeToCustomer' => 0,
                'billPaymentChannel' => 0,
                'enableFPXB2B' => 1,
                'chargeFPXB2B' => 0,
            ];

            $result = $this->toyyibPayService->createBill($billData);

            if (!$result['success']) {
                $this->bookingService->handlePaymentFailure($booking, $result['error']);

                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Unable to initiate payment. Please try again.');
            }

            $billCode = $result['data'][0]['BillCode'];

            $this->bookingService->initiatePayment($booking, $billCode);

            $paymentUrl = $this->toyyibPayService->getPaymentUrl($billCode);

            return redirect()->away($paymentUrl);
        } catch (RuntimeException $e) {
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
