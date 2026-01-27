<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $todayRevenue = Booking::query()
            ->whereDate('created_at', now()->toDateString())
            ->where('status', Booking::STATUS_CONFIRMED)
            ->sum('total');

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
