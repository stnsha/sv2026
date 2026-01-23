<?php

namespace App\Services;

use App\Models\BlockedTable;
use App\Models\Booking;
use App\Models\Date;
use App\Models\Table;
use App\Models\TableBooking;
use App\Models\TimeSlot;

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

    public function getSlotCapacitySummary(Date $date, TimeSlot $timeSlot): array
    {
        $allTables = Table::all();
        $totalTables = $allTables->count();
        $totalCapacity = $allTables->sum('capacity');

        $blockedTableIds = $this->getBlockedTableIds($date->id, $timeSlot->id);
        $blockedTables = count($blockedTableIds);
        $blockedCapacity = $allTables->whereIn('id', $blockedTableIds)->sum('capacity');

        $confirmedTableIds = $this->getBookedTableIds($date->id, $timeSlot->id);
        $confirmedTables = count($confirmedTableIds);
        $confirmedCapacity = $allTables->whereIn('id', $confirmedTableIds)->sum('capacity');

        $availableTables = $totalTables - $blockedTables - $confirmedTables;
        $availableCapacity = $totalCapacity - $blockedCapacity - $confirmedCapacity;

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

    public function getDateCapacitySummaryBySlot(Date $date): array
    {
        $timeSlots = TimeSlot::all();
        $summaries = [];

        foreach ($timeSlots as $timeSlot) {
            $summaries[$timeSlot->id] = $this->getSlotCapacitySummary($date, $timeSlot);
        }

        return $summaries;
    }

    public function syncBlockedTablesForSlot(int $dateId, int $timeSlotId, array $tableIds): void
    {
        BlockedTable::query()
            ->where('date_id', $dateId)
            ->where('time_slot_id', $timeSlotId)
            ->delete();

        foreach ($tableIds as $tableId) {
            BlockedTable::create([
                'table_id' => $tableId,
                'date_id' => $dateId,
                'time_slot_id' => $timeSlotId,
            ]);
        }
    }

    public function getBookedTableIds(int $dateId, int $timeSlotId): array
    {
        return TableBooking::query()
            ->where('date_id', $dateId)
            ->where('time_slot_id', $timeSlotId)
            ->whereHas('booking', function ($query) {
                $query->where('status', Booking::STATUS_CONFIRMED);
            })
            ->pluck('table_id')
            ->unique()
            ->toArray();
    }

    public function getBlockedTableIds(int $dateId, int $timeSlotId): array
    {
        return BlockedTable::query()
            ->where('date_id', $dateId)
            ->where('time_slot_id', $timeSlotId)
            ->pluck('table_id')
            ->toArray();
    }
}
