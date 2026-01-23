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

        $dates = Date::query()
            ->orderBy('date_value', $sortDirection)
            ->paginate(15)
            ->withQueryString();

        $timeSlots = TimeSlot::all();
        $allTables = Table::all();
        $totalTables = $allTables->count();
        $totalCapacity = $allTables->sum('capacity');

        $dateSummaries = [];
        foreach ($dates as $date) {
            $dateSummaries[$date->id] = $this->capacityService->getDateCapacitySummaryBySlot($date);
        }

        return view('admin.capacity.index', [
            'dates' => $dates,
            'timeSlots' => $timeSlots,
            'dateSummaries' => $dateSummaries,
            'totalTables' => $totalTables,
            'totalCapacity' => $totalCapacity,
            'sortDirection' => $sortDirection,
        ]);
    }

    public function edit(Date $date, TimeSlot $timeSlot): View
    {
        $tables = Table::query()->orderBy('table_number')->get();
        $blockedTableIds = $this->capacityService->getBlockedTableIds($date->id, $timeSlot->id);
        $bookedTableIds = $this->capacityService->getBookedTableIds($date->id, $timeSlot->id);

        return view('admin.capacity.edit', [
            'date' => $date,
            'timeSlot' => $timeSlot,
            'tables' => $tables,
            'blockedTableIds' => $blockedTableIds,
            'bookedTableIds' => $bookedTableIds,
        ]);
    }

    public function update(Request $request, Date $date, TimeSlot $timeSlot): RedirectResponse
    {
        $validated = $request->validate([
            'blocked_tables' => 'nullable|array',
            'blocked_tables.*' => 'exists:tables,id',
        ]);

        $blockedTableIds = $validated['blocked_tables'] ?? [];
        $this->capacityService->syncBlockedTablesForSlot($date->id, $timeSlot->id, $blockedTableIds);

        return redirect()
            ->route('admin.capacity.index')
            ->with('success', 'Blocked tables updated for ' . $date->formatted_date . ' - ' . $timeSlot->formatted_time);
    }
}
