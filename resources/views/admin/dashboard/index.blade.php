@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', 'Overview of your restaurant bookings and performance')

@section('content')
    <!-- Bento-style stats cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
        <!-- Today's Bookings Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow border-l-4 border-secondary-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-grey-500 mb-1">Today's Bookings</p>
                    <p class="text-3xl font-bold text-grey-900">{{ $todayBookings }}</p>
                    <p class="text-xs text-grey-400 mt-1">Confirmed reservations</p>
                </div>
                <div class="p-3 bg-secondary-100 rounded-lg">
                    <img src="{{ asset('img/tracking_13333232.png') }}" alt="Bookings" class="w-10 h-10">
                </div>
            </div>
        </div>

        <!-- Weekly Revenue Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow border-l-4 border-secondary-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-grey-500 mb-1">Weekly Revenue</p>
                    <p class="text-3xl font-bold text-grey-900">RM {{ number_format($weeklyRevenue, 2) }}</p>
                    <p class="text-xs text-grey-400 mt-1">From confirmed bookings</p>
                </div>
                <div class="p-3 bg-secondary-100 rounded-lg">
                    <img src="{{ asset('img/extra-time_8727099.png') }}" alt="Revenue" class="w-10 h-10">
                </div>
            </div>
        </div>

        <!-- Total Customers Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow border-l-4 border-secondary-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-grey-500 mb-1">Total Customers</p>
                    <p class="text-3xl font-bold text-grey-900">{{ $totalCustomers }}</p>
                    <p class="text-xs text-grey-400 mt-1">Registered customers</p>
                </div>
                <div class="p-3 bg-secondary-100 rounded-lg">
                    <img src="{{ asset('img/family_3010442.png') }}" alt="Customers" class="w-10 h-10">
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings Table -->
    <div class="mt-6 lg:mt-8">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-4 lg:px-6 py-4 border-b border-grey-200 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-grey-900">Recent Bookings</h2>
                    <p class="text-sm text-grey-500">Latest reservation activity</p>
                </div>
                <a href="{{ route('admin.bookings.index') }}" class="text-sm text-primary-600 hover:text-primary-700 hover:underline">
                    View all
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-grey-200">
                    <thead class="bg-grey-50">
                        <tr>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider">
                                Customer
                            </th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider hidden md:table-cell">
                                Time
                            </th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-4 lg:px-6 py-3 text-right text-xs font-medium text-grey-500 uppercase tracking-wider hidden sm:table-cell">
                                Total
                            </th>
                            <th class="px-4 lg:px-6 py-3 text-right text-xs font-medium text-grey-500 uppercase tracking-wider">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-grey-200">
                        @forelse($recentBookings as $booking)
                            <tr class="hover:bg-grey-50">
                                <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-grey-900">{{ $booking->customer->name }}</div>
                                    <div class="text-xs text-grey-500 md:hidden">{{ $booking->timeSlot->formatted_time ?? '-' }}</div>
                                </td>
                                <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-grey-600">
                                    {{ $booking->date->formatted_date }}
                                </td>
                                <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-grey-600 hidden md:table-cell">
                                    {{ $booking->timeSlot->formatted_time ?? '-' }}
                                </td>
                                <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                    @switch($booking->status)
                                        @case(\App\Models\Booking::STATUS_INITIATED)
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-grey-100 text-grey-700">
                                                Initiated
                                            </span>
                                            @break
                                        @case(\App\Models\Booking::STATUS_PENDING_PAYMENT)
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-warning-100 text-warning-700">
                                                Pending
                                            </span>
                                            @break
                                        @case(\App\Models\Booking::STATUS_CONFIRMED)
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-success-100 text-success-700">
                                                Confirmed
                                            </span>
                                            @break
                                        @case(\App\Models\Booking::STATUS_CANCELLED)
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-danger-100 text-danger-700">
                                                Cancelled
                                            </span>
                                            @break
                                        @case(\App\Models\Booking::STATUS_PAYMENT_FAILED)
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-danger-100 text-danger-700">
                                                Failed
                                            </span>
                                            @break
                                        @default
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-grey-100 text-grey-700">
                                                Unknown
                                            </span>
                                    @endswitch
                                </td>
                                <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-grey-600 text-right hidden sm:table-cell">
                                    RM {{ number_format($booking->total, 2) }}
                                </td>
                                <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.bookings.show', $booking) }}" class="text-primary-600 hover:text-primary-700">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 lg:px-6 py-8 text-center text-grey-500">
                                    No bookings found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
