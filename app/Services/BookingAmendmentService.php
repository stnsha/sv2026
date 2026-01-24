<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\TableBooking;
use Illuminate\Support\Facades\DB;

class BookingAmendmentService
{
    public function __construct(
        private TableAssignmentService $tableAssignmentService
    ) {}

    public function checkAmendmentAvailability(Booking $booking, int $dateId, int $timeSlotId): array
    {
        $totalPax = $booking->details->sum('quantity');

        $availableTablesExcludingCurrent = $this->getAvailableTablesExcludingBooking(
            $booking,
            $dateId,
            $timeSlotId
        );

        $availableCapacity = $availableTablesExcludingCurrent->sum('capacity');

        if ($availableCapacity < $totalPax) {
            return [
                'available' => false,
                'message' => "Insufficient capacity. Need {$totalPax} pax, only {$availableCapacity} available.",
                'available_capacity' => $availableCapacity,
                'required_capacity' => $totalPax,
            ];
        }

        return [
            'available' => true,
            'message' => 'Tables available for this slot.',
            'available_capacity' => $availableCapacity,
            'required_capacity' => $totalPax,
        ];
    }

    public function amendBooking(Booking $booking, int $dateId, int $timeSlotId): bool
    {
        $totalPax = $booking->details->sum('quantity');

        return DB::transaction(function () use ($booking, $dateId, $timeSlotId, $totalPax) {
            $availableTables = $this->getAvailableTablesExcludingBooking($booking, $dateId, $timeSlotId);

            $optimalTables = $this->findOptimalTablesFromCollection($availableTables, $totalPax);

            if ($optimalTables === null) {
                return false;
            }

            TableBooking::where('booking_id', $booking->id)->delete();

            foreach ($optimalTables['tables'] as $table) {
                TableBooking::create([
                    'booking_id' => $booking->id,
                    'date_id' => $dateId,
                    'time_slot_id' => $timeSlotId,
                    'table_id' => $table->id,
                ]);
            }

            $booking->update([
                'date_id' => $dateId,
                'time_slot_id' => $timeSlotId,
            ]);

            return true;
        });
    }

    private function getAvailableTablesExcludingBooking(Booking $booking, int $dateId, int $timeSlotId)
    {
        $isSameSlot = $booking->date_id === $dateId && $booking->time_slot_id === $timeSlotId;

        if ($isSameSlot) {
            return $this->tableAssignmentService->getAvailableTables($dateId, $timeSlotId);
        }

        $currentTableIds = $booking->tableBookings->pluck('table_id')->toArray();

        $availableTables = $this->tableAssignmentService->getAvailableTables($dateId, $timeSlotId);

        return $availableTables;
    }

    private function findOptimalTablesFromCollection($availableTables, int $totalPax): ?array
    {
        $sixSeaters = $availableTables->where('capacity', 6)->values();
        $fourSeaters = $availableTables->where('capacity', 4)->values();

        $maxSixSeaters = $sixSeaters->count();
        $maxFourSeaters = $fourSeaters->count();

        $bestCombination = null;
        $minWaste = PHP_INT_MAX;
        $minTables = PHP_INT_MAX;

        for ($numSix = 0; $numSix <= $maxSixSeaters; $numSix++) {
            for ($numFour = 0; $numFour <= $maxFourSeaters; $numFour++) {
                $totalCapacity = ($numSix * 6) + ($numFour * 4);

                if ($totalCapacity >= $totalPax) {
                    $waste = $totalCapacity - $totalPax;
                    $totalTables = $numSix + $numFour;

                    $isBetter = $waste < $minWaste
                        || ($waste === $minWaste && $totalTables < $minTables);

                    if ($isBetter) {
                        $minWaste = $waste;
                        $minTables = $totalTables;
                        $bestCombination = [
                            'six_seaters' => $numSix,
                            'four_seaters' => $numFour,
                        ];
                    }

                    break;
                }
            }
        }

        if ($bestCombination === null) {
            return null;
        }

        $selectedTables = collect();
        $selectedTables = $selectedTables->merge(
            $sixSeaters->take($bestCombination['six_seaters'])
        );
        $selectedTables = $selectedTables->merge(
            $fourSeaters->take($bestCombination['four_seaters'])
        );

        return [
            'tables' => $selectedTables,
        ];
    }
}
