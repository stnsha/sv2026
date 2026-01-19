<?php

namespace App\Services;

use App\Models\Table;
use App\Models\TableBooking;
use Illuminate\Support\Collection;

class TableAssignmentService
{
    public function findOptimalTables(int $totalPax, int $dateId, int $timeSlotId): ?array
    {
        $availableTables = $this->getAvailableTables($dateId, $timeSlotId);

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
                            'total_capacity' => $totalCapacity,
                            'waste' => $waste,
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
            'six_seaters' => $bestCombination['six_seaters'],
            'four_seaters' => $bestCombination['four_seaters'],
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
                    \App\Models\Booking::STATUS_INITIATED,
                    \App\Models\Booking::STATUS_PENDING_PAYMENT,
                    \App\Models\Booking::STATUS_CONFIRMED,
                ]);
            })
            ->pluck('table_id');

        return Table::query()
            ->whereNotIn('id', $bookedTableIds)
            ->orderBy('capacity', 'desc')
            ->get();
    }

    public function getAvailabilitySummary(int $dateId, int $timeSlotId): array
    {
        $allTables = Table::all();
        $availableTables = $this->getAvailableTables($dateId, $timeSlotId);

        $totalSixSeaters = $allTables->where('capacity', 6)->count();
        $totalFourSeaters = $allTables->where('capacity', 4)->count();
        $availableSixSeaters = $availableTables->where('capacity', 6)->count();
        $availableFourSeaters = $availableTables->where('capacity', 4)->count();

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
            'total_capacity' => ($totalSixSeaters * 6) + ($totalFourSeaters * 4),
            'available_capacity' => ($availableSixSeaters * 6) + ($availableFourSeaters * 4),
        ];
    }
}
