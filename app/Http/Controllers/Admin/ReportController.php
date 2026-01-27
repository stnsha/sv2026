<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
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
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        $thisMonthBookings = Booking::query()
            ->where('status', Booking::STATUS_CONFIRMED)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
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

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);

            $query = Booking::query()
                ->where('status', Booking::STATUS_CONFIRMED)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year);

            $bookingCount = (clone $query)->count();

            if ($bookingCount === 0) {
                continue;
            }

            $revenue = (clone $query)->sum('total');
            $average = $revenue / $bookingCount;

            $stats[] = [
                'month' => $date->format('F Y'),
                'bookings' => $bookingCount,
                'revenue' => $revenue,
                'average' => $average,
            ];
        }

        return $stats;
    }
}
