<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Date;
use App\Models\Table;
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

        $allTables = Table::all();
        $totalTables = $allTables->count();
        $totalCapacity = $allTables->sum('capacity');

        $dateSummaries = [];
        foreach ($dates as $date) {
            $dateSummaries[$date->id] = $this->capacityService->getDateCapacitySummary($date);
        }

        return view('admin.capacity.index', [
            'dates' => $dates,
            'dateSummaries' => $dateSummaries,
            'totalTables' => $totalTables,
            'totalCapacity' => $totalCapacity,
            'sortDirection' => $sortDirection,
        ]);
    }

    public function edit(Date $date): View
    {
        $tables = Table::query()->orderBy('table_number')->get();
        $blockedTableIds = $date->blockedTables()->pluck('table_id')->toArray();

        return view('admin.capacity.edit', [
            'date' => $date,
            'tables' => $tables,
            'blockedTableIds' => $blockedTableIds,
        ]);
    }

    public function update(Request $request, Date $date): RedirectResponse
    {
        $validated = $request->validate([
            'blocked_tables' => 'nullable|array',
            'blocked_tables.*' => 'exists:tables,id',
        ]);

        $blockedTableIds = $validated['blocked_tables'] ?? [];

        $this->capacityService->syncBlockedTables($date->id, $blockedTableIds);

        return redirect()
            ->route('admin.capacity.index')
            ->with('success', 'Blocked tables updated successfully for ' . $date->formatted_date);
    }
}
