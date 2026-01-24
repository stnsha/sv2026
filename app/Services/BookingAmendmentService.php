<?php

namespace App\Services;

use App\Models\BlockedTable;
use App\Models\Booking;
use App\Models\Table;
use App\Models\TableBooking;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BookingAmendmentService
{
    public function __construct(
        private TableAssignmentService $tableAssignmentService
    ) {}

    public function checkAmendmentAvailability(
        Booking $booking,
        int $dateId,
        int $timeSlotId,
        ?array $tableIds = null
    ): array {
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
                'available_tables' => [],
                'current_tables_available' => false,
            ];
        }

        $currentTableIds = $booking->tableBookings->pluck('table_id')->toArray();
        $availableTableIds = $availableTablesExcludingCurrent->pluck('id')->toArray();
        $currentTablesAvailable = empty(array_diff($currentTableIds, $availableTableIds));

        if ($tableIds !== null) {
            $selectedTables = Table::whereIn('id', $tableIds)->get();
            $selectedCapacity = $selectedTables->sum('capacity');

            $unavailableSelectedIds = array_diff($tableIds, $availableTableIds);
            if (!empty($unavailableSelectedIds)) {
                return [
                    'available' => false,
                    'message' => 'Some selected tables are not available for this slot.',
                    'available_capacity' => $availableCapacity,
                    'required_capacity' => $totalPax,
                    'available_tables' => $this->formatTablesForResponse($availableTablesExcludingCurrent),
                    'current_tables_available' => $currentTablesAvailable,
                ];
            }

            if ($selectedCapacity < $totalPax) {
                return [
                    'available' => false,
                    'message' => "Selected tables have insufficient capacity. Need {$totalPax} pax, selected {$selectedCapacity}.",
                    'available_capacity' => $availableCapacity,
                    'required_capacity' => $totalPax,
                    'available_tables' => $this->formatTablesForResponse($availableTablesExcludingCurrent),
                    'current_tables_available' => $currentTablesAvailable,
                ];
            }
        }

        $suggestedTableIds = $this->getSuggestedTableIds(
            $booking,
            $availableTablesExcludingCurrent,
            $totalPax,
            $currentTablesAvailable
        );

        return [
            'available' => true,
            'message' => 'Tables available for this slot.',
            'available_capacity' => $availableCapacity,
            'required_capacity' => $totalPax,
            'available_tables' => $this->formatTablesForResponse($availableTablesExcludingCurrent),
            'current_tables_available' => $currentTablesAvailable,
            'suggested_table_ids' => $suggestedTableIds,
        ];
    }

    public function amendBooking(
        Booking $booking,
        int $dateId,
        int $timeSlotId,
        ?array $tableIds = null
    ): bool {
        $totalPax = $booking->details->sum('quantity');

        return DB::transaction(function () use ($booking, $dateId, $timeSlotId, $totalPax, $tableIds) {
            if ($tableIds !== null) {
                $tablesToAssign = Table::whereIn('id', $tableIds)->get();
            } else {
                $availableTables = $this->getAvailableTablesExcludingBooking($booking, $dateId, $timeSlotId);
                $optimalResult = $this->findOptimalTablesFromCollection($availableTables, $totalPax);

                if ($optimalResult === null) {
                    return false;
                }

                $tablesToAssign = $optimalResult['tables'];
            }

            TableBooking::where('booking_id', $booking->id)->delete();

            foreach ($tablesToAssign as $table) {
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

    public function getAvailableTablesForAmendment(
        Booking $booking,
        int $dateId,
        int $timeSlotId
    ): Collection {
        return $this->getAvailableTablesExcludingBooking($booking, $dateId, $timeSlotId);
    }

    private function getSuggestedTableIds(
        Booking $booking,
        Collection $availableTables,
        int $totalPax,
        bool $currentTablesAvailable
    ): array {
        if ($currentTablesAvailable) {
            return $booking->tableBookings->pluck('table_id')->toArray();
        }

        $optimalResult = $this->findOptimalTablesFromCollection($availableTables, $totalPax);

        if ($optimalResult === null) {
            return [];
        }

        return $optimalResult['tables']->pluck('id')->toArray();
    }

    private function formatTablesForResponse(Collection $tables): array
    {
        return $tables->map(function ($table) {
            return [
                'id' => $table->id,
                'table_number' => $table->table_number,
                'capacity' => $table->capacity,
                'seat_type' => $table->seat_type,
            ];
        })->values()->toArray();
    }

    private function getAvailableTablesExcludingBooking(Booking $booking, int $dateId, int $timeSlotId): Collection
    {
        $blockedTableIds = BlockedTable::query()
            ->where('date_id', $dateId)
            ->where('time_slot_id', $timeSlotId)
            ->pluck('table_id')
            ->toArray();

        $bookedByOthersTableIds = TableBooking::query()
            ->where('date_id', $dateId)
            ->where('time_slot_id', $timeSlotId)
            ->where('booking_id', '!=', $booking->id)
            ->whereHas('booking', function ($query) {
                $query->whereIn('status', [
                    Booking::STATUS_INITIATED,
                    Booking::STATUS_PENDING_PAYMENT,
                    Booking::STATUS_CONFIRMED,
                ]);
            })
            ->pluck('table_id')
            ->toArray();

        $unavailableTableIds = array_merge($blockedTableIds, $bookedByOthersTableIds);

        return Table::query()
            ->whereNotIn('id', $unavailableTableIds)
            ->orderBy('capacity', 'desc')
            ->get();
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
