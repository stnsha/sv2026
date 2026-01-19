<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Date;
use App\Models\Table;
use App\Models\TableBooking;
use Illuminate\View\View;

class TableController extends Controller
{
    public function index(): View
    {
        $tables = Table::query()
            ->orderBy('table_number')
            ->get();

        $today = Date::query()
            ->where('date_value', now()->toDateString())
            ->first();

        $bookedTableIds = [];
        $bookedCount = 0;

        if ($today) {
            $bookedTableIds = TableBooking::query()
                ->where('date_id', $today->id)
                ->whereHas('booking', function ($query) {
                    $query->where('status', Booking::STATUS_CONFIRMED);
                })
                ->pluck('table_id')
                ->unique()
                ->toArray();

            $bookedCount = count($bookedTableIds);
        }

        $availableCount = $tables->count() - $bookedCount;

        return view('admin.tables.index', [
            'tables' => $tables,
            'bookedTableIds' => $bookedTableIds,
            'bookedCount' => $bookedCount,
            'availableCount' => $availableCount,
        ]);
    }
}
