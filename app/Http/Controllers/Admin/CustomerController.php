<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = Customer::query()
            ->withCount('bookings')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $totalCustomers = Customer::count();

        $newThisMonth = Customer::query()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $totalBookingsCount = Booking::query()
            ->where('status', Booking::STATUS_CONFIRMED)
            ->count();

        return view('admin.customers.index', [
            'customers' => $customers,
            'totalCustomers' => $totalCustomers,
            'newThisMonth' => $newThisMonth,
            'totalBookingsCount' => $totalBookingsCount,
        ]);
    }
}
