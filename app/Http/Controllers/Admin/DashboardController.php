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

        $todayBookings = 0;
        if ($today) {
            $todayBookings = Booking::query()
                ->where('date_id', $today->id)
                ->where('status', Booking::STATUS_CONFIRMED)
                ->count();
        }

        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $weeklyRevenue = Booking::query()
            ->where('status', Booking::STATUS_CONFIRMED)
            ->whereHas('date', function ($query) use ($startOfWeek, $endOfWeek) {
                $query->whereBetween('date_value', [$startOfWeek->toDateString(), $endOfWeek->toDateString()]);
            })
            ->sum('total');

        $totalCustomers = Customer::count();

        $recentBookings = Booking::query()
            ->with(['customer', 'date', 'timeSlot'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard.index', [
            'todayBookings' => $todayBookings,
            'weeklyRevenue' => $weeklyRevenue,
            'totalCustomers' => $totalCustomers,
            'recentBookings' => $recentBookings,
        ]);
    }
}
