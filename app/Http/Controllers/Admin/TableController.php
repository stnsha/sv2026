<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Date;
use App\Models\Table;
use App\Models\TableBooking;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TableController extends Controller
{
    public function index(Request $request): View
    {
        $today = Date::query()
            ->where('date_value', now()->toDateString())
            ->first();

        $bookedTableIds = [];

        if ($today) {
            $bookedTableIds = TableBooking::query()
                ->where('date_id', $today->id)
                ->whereHas('booking', function ($query) {
                    $query->where('status', Booking::STATUS_CONFIRMED);
                })
                ->pluck('table_id')
                ->unique()
                ->toArray();
        }

        $query = Table::query();

        // Search by table number
        if ($search = $request->input('search')) {
            $query->where('table_number', 'like', "%{$search}%");
        }

        // Filter by seat type
        if ($request->filled('seat_type')) {
            $query->where('seat_type', $request->input('seat_type'));
        }

        // Filter by today's status
        if ($request->filled('today_status')) {
            if ($request->input('today_status') === 'booked') {
                $query->whereIn('id', $bookedTableIds);
            } elseif ($request->input('today_status') === 'available') {
                $query->whereNotIn('id', $bookedTableIds);
            }
        }

        // Sorting
        $sortColumn = $request->input('sort', 'table_number');
        $sortDirection = $request->input('direction', 'asc');
        $allowedSorts = ['table_number', 'seat_type', 'capacity'];

        if (in_array($sortColumn, $allowedSorts)) {
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            $query->orderBy('table_number', 'asc');
        }

        $tables = $query->paginate(10)->withQueryString();

        // Calculate counts from full dataset (unfiltered)
        $totalTables = Table::count();
        $totalCapacity = Table::sum('capacity');
        $bookedCount = count($bookedTableIds);
        $availableCount = $totalTables - $bookedCount;

        // Get distinct seat types for filter dropdown
        $seatTypes = Table::query()
            ->distinct()
            ->orderBy('seat_type')
            ->pluck('seat_type')
            ->toArray();

        return view('admin.tables.index', [
            'tables' => $tables,
            'bookedTableIds' => $bookedTableIds,
            'bookedCount' => $bookedCount,
            'availableCount' => $availableCount,
            'totalTables' => $totalTables,
            'totalCapacity' => $totalCapacity,
            'seatTypes' => $seatTypes,
            'currentSort' => $sortColumn,
            'currentDirection' => $sortDirection,
        ]);
    }
}
