<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingDetails;
use App\Models\Customer;
use App\Models\Price;
use App\Models\TableBooking;
use DateTime;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BookingService
{
    public function __construct(
        private TableAssignmentService $tableAssignmentService
    ) {}

    public function createBooking(array $customerData, int $dateId, int $timeSlotId, array $paxDetails): Booking
    {
        return DB::transaction(function () use ($customerData, $dateId, $timeSlotId, $paxDetails) {
            $totalPax = collect($paxDetails)->sum('quantity');

            $tableResult = $this->tableAssignmentService->findOptimalTables($totalPax, $dateId, $timeSlotId);

            if ($tableResult === null) {
                throw new RuntimeException('Not enough tables available for the requested party size.');
            }

            $customer = Customer::firstOrCreate(
                ['email' => $customerData['email']],
                [
                    'name' => $customerData['name'],
                    'phone_number' => $customerData['phone_number'],
                ]
            );

            if ($customer->wasRecentlyCreated === false) {
                $customer->update([
                    'name' => $customerData['name'],
                    'phone_number' => $customerData['phone_number'],
                ]);
            }

            $subtotal = 0;
            $detailsData = [];

            foreach ($paxDetails as $detail) {
                if ($detail['quantity'] <= 0) {
                    continue;
                }

                $price = Price::findOrFail($detail['price_id']);
                $lineSubtotal = $price->amount * $detail['quantity'];
                $subtotal += $lineSubtotal;

                $detailsData[] = [
                    'price_id' => $detail['price_id'],
                    'quantity' => $detail['quantity'],
                    'subtotal' => $lineSubtotal,
                    'discount' => 0,
                    'total' => $lineSubtotal,
                ];
            }

            $serviceCharge = 1.00;
            $discount = 0;
            $total = $subtotal + $serviceCharge - $discount;

            $booking = Booking::create([
                'customer_id' => $customer->id,
                'date_id' => $dateId,
                'time_slot_id' => $timeSlotId,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'service_charge' => $serviceCharge,
                'total' => $total,
                'status' => Booking::STATUS_INITIATED,
            ]);

            $booking->update([
                'reference_id' => Booking::generateReferenceId($booking->id, $dateId),
            ]);

            foreach ($detailsData as $detailData) {
                BookingDetails::create([
                    'booking_id' => $booking->id,
                    ...$detailData,
                ]);
            }

            foreach ($tableResult['tables'] as $table) {
                TableBooking::create([
                    'booking_id' => $booking->id,
                    'date_id' => $dateId,
                    'time_slot_id' => $timeSlotId,
                    'table_id' => $table->id,
                ]);
            }

            return $booking->load(['customer', 'date', 'timeSlot', 'details.price', 'tableBookings.table']);
        });
    }

    public function initiatePayment(Booking $booking, string $billCode): Booking
    {
        $booking->update([
            'bill_code' => $billCode,
            'status' => Booking::STATUS_PENDING_PAYMENT,
        ]);

        return $booking->fresh();
    }

    public function confirmBooking(Booking $booking, string $transactionRef, DateTime $transactionTime): Booking
    {
        $booking->update([
            'status' => Booking::STATUS_CONFIRMED,
            'transaction_reference_no' => $transactionRef,
            'transaction_time' => $transactionTime,
            'status_message' => 'Payment successful',
        ]);

        return $booking->fresh();
    }

    public function handlePaymentFailure(Booking $booking, string $errorMessage): Booking
    {
        return DB::transaction(function () use ($booking, $errorMessage) {
            $booking->update([
                'status' => Booking::STATUS_PAYMENT_FAILED,
                'status_message' => $errorMessage,
            ]);

            TableBooking::where('booking_id', $booking->id)->delete();

            return $booking->fresh();
        });
    }

    public function cancelBooking(Booking $booking, string $reason): Booking
    {
        return DB::transaction(function () use ($booking, $reason) {
            $booking->update([
                'status' => Booking::STATUS_CANCELLED,
                'status_message' => $reason,
            ]);

            TableBooking::where('booking_id', $booking->id)->delete();

            return $booking->fresh();
        });
    }

    public function checkAvailability(int $dateId, int $timeSlotId, int $totalPax): array
    {
        $tableResult = $this->tableAssignmentService->findOptimalTables($totalPax, $dateId, $timeSlotId);
        $summary = $this->tableAssignmentService->getAvailabilitySummary($dateId, $timeSlotId);

        return [
            'available' => $tableResult !== null,
            'tables_needed' => $tableResult ? [
                'six_seaters' => $tableResult['six_seaters'],
                'four_seaters' => $tableResult['four_seaters'],
                'total_capacity' => $tableResult['total_capacity'],
            ] : null,
            'summary' => $summary,
        ];
    }
}
