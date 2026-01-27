<?php

namespace App\Services;

use App\Models\BlockedTable;
use App\Models\Booking;
use App\Models\Table;
use App\Models\TableBooking;
use App\Models\TableCapacityOverride;
use Illuminate\Support\Collection;

class TableAssignmentService
{
    public function findOptimalTables(int $totalPax, int $dateId, int $timeSlotId): ?array
    {
        $availableTables = $this->getAvailableTables($dateId, $timeSlotId);

        $sixSeaters = $availableTables->where('effective_capacity', 6)->values();
        $fourSeaters = $availableTables->where('effective_capacity', 4)->values();
        $twoSeaters = $availableTables->where('effective_capacity', 2)->values();

        $maxSixSeaters = $sixSeaters->count();
        $maxFourSeaters = $fourSeaters->count();
        $maxTwoSeaters = $twoSeaters->count();

        $bestCombination = null;
        $minWaste = PHP_INT_MAX;
        $minTables = PHP_INT_MAX;

        for ($numSix = 0; $numSix <= $maxSixSeaters; $numSix++) {
            for ($numFour = 0; $numFour <= $maxFourSeaters; $numFour++) {
                for ($numTwo = 0; $numTwo <= $maxTwoSeaters; $numTwo++) {
                    $totalCapacity = ($numSix * 6) + ($numFour * 4) + ($numTwo * 2);

                    if ($totalCapacity >= $totalPax) {
                        $waste = $totalCapacity - $totalPax;
                        $totalTables = $numSix + $numFour + $numTwo;

                        $isBetter = $waste < $minWaste
                            || ($waste === $minWaste && $totalTables < $minTables);

                        if ($isBetter) {
                            $minWaste = $waste;
                            $minTables = $totalTables;
                            $bestCombination = [
                                'six_seaters' => $numSix,
                                'four_seaters' => $numFour,
                                'two_seaters' => $numTwo,
                                'total_capacity' => $totalCapacity,
                                'waste' => $waste,
                            ];
                        }

                        break;
                    }
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
        $selectedTables = $selectedTables->merge(
            $twoSeaters->take($bestCombination['two_seaters'])
        );

        return [
            'tables' => $selectedTables,
            'six_seaters' => $bestCombination['six_seaters'],
            'four_seaters' => $bestCombination['four_seaters'],
            'two_seaters' => $bestCombination['two_seaters'],
            'total_capacity' => $bestCombination['total_capacity'],
            'waste' => $bestCombination['waste'],
        ];
    }

    public function getAvailableTables(int $dateId, int $timeSlotId): Collection
    {
        $bookedTableIds = TableBooking::query()
            ->where('date_id', $dateId)
            ->where('time_slot_id', $timeSlotId)
            ->whereHas('booking', function ($query) {
                $query->whereIn('status', [
                    Booking::STATUS_INITIATED,
                    Booking::STATUS_PENDING_PAYMENT,
                    Booking::STATUS_CONFIRMED,
                ]);
            })
            ->pluck('table_id');

        $blockedTableIds = BlockedTable::query()
            ->where('date_id', $dateId)
            ->where('time_slot_id', $timeSlotId)
            ->pluck('table_id');

        $capacityOverrides = TableCapacityOverride::query()
            ->where('date_id', $dateId)
            ->where('time_slot_id', $timeSlotId)
            ->pluck('effective_capacity', 'table_id');

        $tables = Table::query()
            ->whereNotIn('id', $bookedTableIds)
            ->whereNotIn('id', $blockedTableIds)
            ->get();

        $tables->each(function ($table) use ($capacityOverrides) {
            $table->effective_capacity = $capacityOverrides->get($table->id, $table->capacity);
        });

        return $tables->sortByDesc('effective_capacity')->values();
    }

    public function getAvailabilitySummary(int $dateId, int $timeSlotId): array
    {
        $allTables = Table::all();
        $availableTables = $this->getAvailableTables($dateId, $timeSlotId);

        $totalSixSeaters = $allTables->where('capacity', 6)->count();
        $totalFourSeaters = $allTables->where('capacity', 4)->count();
        $availableSixSeaters = $availableTables->where('effective_capacity', 6)->count();
        $availableFourSeaters = $availableTables->where('effective_capacity', 4)->count();
        $availableTwoSeaters = $availableTables->where('effective_capacity', 2)->count();

        $availableCapacity = ($availableSixSeaters * 6) + ($availableFourSeaters * 4) + ($availableTwoSeaters * 2);

        return [
            'total_tables' => $allTables->count(),
            'available_tables' => $availableTables->count(),
            'booked_tables' => $allTables->count() - $availableTables->count(),
            'total_six_seaters' => $totalSixSeaters,
            'available_six_seaters' => $availableSixSeaters,
            'booked_six_seaters' => $totalSixSeaters - $availableSixSeaters,
            'total_four_seaters' => $totalFourSeaters,
            'available_four_seaters' => $availableFourSeaters,
            'booked_four_seaters' => $totalFourSeaters - $availableFourSeaters,
            'available_two_seaters' => $availableTwoSeaters,
            'total_capacity' => ($totalSixSeaters * 6) + ($totalFourSeaters * 4),
            'available_capacity' => $availableCapacity,
        ];
    }
}
