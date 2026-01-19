@extends('layouts.dashboard')

@section('title', 'Reports')
@section('page-title', 'Reports')
@section('page-description', 'Analytics and performance metrics')

@section('content')
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 mb-6">
        <!-- Total Revenue Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow border-l-4 border-success-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-grey-500 mb-1">Total Revenue</p>
                    <p class="text-3xl font-bold text-grey-900">RM {{ number_format($totalRevenue, 2) }}</p>
                    <p class="text-xs text-grey-400 mt-1">From confirmed bookings</p>
                </div>
                <div class="p-3 bg-success-100 rounded-lg">
                    <svg class="w-10 h-10 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Bookings Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow border-l-4 border-info-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-grey-500 mb-1">Total Bookings</p>
                    <p class="text-3xl font-bold text-grey-900">{{ $totalBookings }}</p>
                    <p class="text-xs text-grey-400 mt-1">All confirmed reservations</p>
                </div>
                <div class="p-3 bg-info-100 rounded-lg">
                    <svg class="w-10 h-10 text-info-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Average Booking Value Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow border-l-4 border-secondary-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-grey-500 mb-1">Average Booking Value</p>
                    <p class="text-3xl font-bold text-grey-900">RM {{ number_format($averageBookingValue, 2) }}</p>
                    <p class="text-xs text-grey-400 mt-1">Per confirmed booking</p>
                </div>
                <div class="p-3 bg-secondary-100 rounded-lg">
                    <svg class="w-10 h-10 text-secondary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-sm text-grey-500">This Month Revenue</p>
            <p class="text-xl font-bold text-grey-900">RM {{ number_format($thisMonthRevenue, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-sm text-grey-500">This Month Bookings</p>
            <p class="text-xl font-bold text-grey-900">{{ $thisMonthBookings }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-sm text-grey-500">Total Customers</p>
            <p class="text-xl font-bold text-grey-900">{{ $totalCustomers }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-sm text-grey-500">Cancellation Rate</p>
            <p class="text-xl font-bold {{ $cancellationRate > 10 ? 'text-danger-600' : 'text-success-600' }}">{{ number_format($cancellationRate, 1) }}%</p>
        </div>
    </div>

    <!-- Monthly Breakdown -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-4 lg:px-6 py-4 border-b border-grey-200">
            <h2 class="text-lg font-semibold text-grey-900">Monthly Summary</h2>
            <p class="text-sm text-grey-500">Performance over the last 6 months</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-grey-200">
                <thead class="bg-grey-50">
                    <tr>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider">
                            Month
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-center text-xs font-medium text-grey-500 uppercase tracking-wider">
                            Bookings
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-right text-xs font-medium text-grey-500 uppercase tracking-wider">
                            Revenue
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-right text-xs font-medium text-grey-500 uppercase tracking-wider hidden sm:table-cell">
                            Avg Value
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-grey-200">
                    @forelse($monthlyStats as $stat)
                        <tr class="hover:bg-grey-50">
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm font-medium text-grey-900">
                                {{ $stat['month'] }}
                            </td>
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-grey-600 text-center">
                                {{ $stat['bookings'] }}
                            </td>
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-grey-600 text-right">
                                RM {{ number_format($stat['revenue'], 2) }}
                            </td>
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-grey-600 text-right hidden sm:table-cell">
                                RM {{ number_format($stat['average'], 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 lg:px-6 py-8 text-center text-grey-500">
                                No data available
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
