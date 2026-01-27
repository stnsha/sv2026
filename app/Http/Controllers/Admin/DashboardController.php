<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Date;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Date::query()
            ->where('date_value', now()->toDateString())
            ->first();

        $todayRevenue = 0;
        if ($today) {
            $todayRevenue = Booking::query()
                ->where('date_id', $today->id)
                ->where('status', Booking::STATUS_CONFIRMED)
                ->sum('total');
        }

        $confirmedBookings = Booking::query()
            ->where('status', Booking::STATUS_CONFIRMED)
            ->count();

        $totalCustomers = Customer::count();

        $recentBookings = Booking::query()
            ->with(['customer', 'date', 'timeSlot'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard.index', [
            'todayRevenue' => $todayRevenue,
            'confirmedBookings' => $confirmedBookings,
            'totalCustomers' => $totalCustomers,
            'recentBookings' => $recentBookings,
        ]);
    }
}
