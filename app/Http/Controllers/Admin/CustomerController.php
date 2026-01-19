<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::query()->withCount('bookings');

        // Search by name, email, phone
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortColumn = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');
        $allowedSorts = ['name', 'email', 'created_at', 'bookings_count'];

        if (in_array($sortColumn, $allowedSorts)) {
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $customers = $query->paginate(10)->withQueryString();

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
            'currentSort' => $sortColumn,
            'currentDirection' => $sortDirection,
        ]);
    }
}
