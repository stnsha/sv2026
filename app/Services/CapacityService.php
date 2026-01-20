<?php

namespace App\Services;

use App\Models\BlockedTable;
use App\Models\Booking;
use App\Models\Date;
use App\Models\Table;
use App\Models\TableBooking;

class CapacityService
{
    public function getDateCapacitySummary(Date $date): array
    {
        $allTables = Table::all();
        $totalTables = $allTables->count();
        $totalCapacity = $allTables->sum('capacity');

        $blockedTableIds = BlockedTable::query()
            ->where('date_id', $date->id)
            ->pluck('table_id');

        $blockedTables = $blockedTableIds->count();
        $blockedCapacity = $allTables->whereIn('id', $blockedTableIds)->sum('capacity');

        $confirmedTableIds = TableBooking::query()
            ->where('date_id', $date->id)
            ->whereHas('booking', function ($query) {
                $query->where('status', Booking::STATUS_CONFIRMED);
            })
            ->pluck('table_id')
            ->unique();

        $confirmedTables = $confirmedTableIds->count();
        $confirmedCapacity = $allTables->whereIn('id', $confirmedTableIds)->sum('capacity');

        $availableTables = $totalTables - $blockedTables;
        $availableCapacity = $totalCapacity - $blockedCapacity;

        return [
            'total_tables' => $totalTables,
            'total_capacity' => $totalCapacity,
            'available_tables' => $availableTables,
            'available_capacity' => $availableCapacity,
            'confirmed_tables' => $confirmedTables,
            'confirmed_capacity' => $confirmedCapacity,
            'blocked_tables' => $blockedTables,
            'blocked_capacity' => $blockedCapacity,
        ];
    }

    public function syncBlockedTables(int $dateId, array $tableIds): void
    {
        BlockedTable::query()->where('date_id', $dateId)->delete();

        foreach ($tableIds as $tableId) {
            BlockedTable::create([
                'table_id' => $tableId,
                'date_id' => $dateId,
            ]);
        }
    }
}
