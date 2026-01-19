<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $totalRevenue = Booking::query()
            ->where('status', Booking::STATUS_CONFIRMED)
            ->sum('total');

        $totalBookings = Booking::query()
            ->where('status', Booking::STATUS_CONFIRMED)
            ->count();

        $averageBookingValue = $totalBookings > 0 ? $totalRevenue / $totalBookings : 0;

        $thisMonthRevenue = Booking::query()
            ->where('status', Booking::STATUS_CONFIRMED)
            ->whereHas('date', function ($query) {
                $query->whereMonth('date_value', now()->month)
                    ->whereYear('date_value', now()->year);
            })
            ->sum('total');

        $thisMonthBookings = Booking::query()
            ->where('status', Booking::STATUS_CONFIRMED)
            ->whereHas('date', function ($query) {
                $query->whereMonth('date_value', now()->month)
                    ->whereYear('date_value', now()->year);
            })
            ->count();

        $totalCustomers = Customer::count();

        $totalAllBookings = Booking::count();
        $cancelledBookings = Booking::query()
            ->whereIn('status', [Booking::STATUS_CANCELLED, Booking::STATUS_PAYMENT_FAILED])
            ->count();

        $cancellationRate = $totalAllBookings > 0 ? ($cancelledBookings / $totalAllBookings) * 100 : 0;

        $monthlyStats = $this->getMonthlyStats();

        return view('admin.reports.index', [
            'totalRevenue' => $totalRevenue,
            'totalBookings' => $totalBookings,
            'averageBookingValue' => $averageBookingValue,
            'thisMonthRevenue' => $thisMonthRevenue,
            'thisMonthBookings' => $thisMonthBookings,
            'totalCustomers' => $totalCustomers,
            'cancellationRate' => $cancellationRate,
            'monthlyStats' => $monthlyStats,
        ]);
    }

    private function getMonthlyStats(): array
    {
        $stats = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('F Y');

            $bookings = Booking::query()
                ->where('status', Booking::STATUS_CONFIRMED)
                ->whereHas('date', function ($query) use ($date) {
                    $query->whereMonth('date_value', $date->month)
                        ->whereYear('date_value', $date->year);
                })
                ->get();

            $bookingCount = $bookings->count();
            $revenue = $bookings->sum('total');
            $average = $bookingCount > 0 ? $revenue / $bookingCount : 0;

            $stats[] = [
                'month' => $month,
                'bookings' => $bookingCount,
                'revenue' => $revenue,
                'average' => $average,
            ];
        }

        return $stats;
    }
}
