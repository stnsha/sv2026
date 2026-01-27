<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Date;
use App\Models\Table;
use App\Models\TimeSlot;
use App\Services\CapacityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CapacityController extends Controller
{
    public function __construct(
        private readonly CapacityService $capacityService
    ) {}

    public function index(Request $request): View
    {
        $sortDirection = $request->input('sort', 'asc');
        $sortDirection = in_array($sortDirection, ['asc', 'desc']) ? $sortDirection : 'asc';

        $datesQuery = Date::query()->orderBy('date_value', $sortDirection);

        if ($request->filled('date')) {
            $datesQuery->where('id', $request->input('date'));
        }

        $dates = $datesQuery->paginate(15)->withQueryString();

        $allTimeSlots = TimeSlot::all();

        $filteredTimeSlots = $request->filled('time_slot')
            ? $allTimeSlots->where('id', (int) $request->input('time_slot'))
            : $allTimeSlots;

        $allTables = Table::all();
        $totalTables = $allTables->count();
        $totalCapacity = $allTables->sum('capacity');

        $dateSummaries = [];
        foreach ($dates as $date) {
            $dateSummaries[$date->id] = $this->capacityService->getDateCapacitySummaryBySlot($date);
        }

        $allDates = Date::query()->orderBy('date_value')->get();
        $dateFilterOptions = $allDates->pluck('formatted_date', 'id')->toArray();
        $timeSlotFilterOptions = $allTimeSlots->pluck('formatted_time', 'id')->toArray();

        return view('admin.capacity.index', [
            'dates' => $dates,
            'timeSlots' => $allTimeSlots,
            'filteredTimeSlots' => $filteredTimeSlots,
            'dateSummaries' => $dateSummaries,
            'totalTables' => $totalTables,
            'totalCapacity' => $totalCapacity,
            'sortDirection' => $sortDirection,
            'dateFilterOptions' => $dateFilterOptions,
            'timeSlotFilterOptions' => $timeSlotFilterOptions,
        ]);
    }

    public function edit(Date $date, TimeSlot $timeSlot): View
    {
        $tables = Table::query()->orderBy('table_number')->get();
        $blockedTableIds = $this->capacityService->getBlockedTableIds($date->id, $timeSlot->id);
        $bookedTableIds = $this->capacityService->getBookedTableIds($date->id, $timeSlot->id);
        $capacityOverrides = $this->capacityService->getCapacityOverrides($date->id, $timeSlot->id);

        return view('admin.capacity.edit', [
            'date' => $date,
            'timeSlot' => $timeSlot,
            'tables' => $tables,
            'blockedTableIds' => $blockedTableIds,
            'bookedTableIds' => $bookedTableIds,
            'capacityOverrides' => $capacityOverrides,
        ]);
    }

    public function update(Request $request, Date $date, TimeSlot $timeSlot): RedirectResponse
    {
        $validated = $request->validate([
            'blocked_tables' => 'nullable|array',
            'blocked_tables.*' => 'exists:tables,id',
            'capacity_overrides' => 'nullable|array',
            'capacity_overrides.*' => 'nullable|integer|min:2',
        ]);

        $blockedTableIds = $validated['blocked_tables'] ?? [];
        $this->capacityService->syncBlockedTablesForSlot($date->id, $timeSlot->id, $blockedTableIds);

        $capacityOverrides = $validated['capacity_overrides'] ?? [];
        $this->capacityService->syncCapacityOverridesForSlot($date->id, $timeSlot->id, $capacityOverrides);

        return redirect()
            ->route('admin.capacity.index')
            ->with('success', 'Table settings updated for ' . $date->formatted_date . ' - ' . $timeSlot->formatted_time);
    }
}
