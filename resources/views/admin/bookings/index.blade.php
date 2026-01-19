@extends('layouts.dashboard')

@section('title', 'Bookings')
@section('page-title', 'Bookings')
@section('page-description', 'View and manage all restaurant reservations')

@section('content')
    <!-- Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-sm text-grey-500">Total Bookings</p>
            <p class="text-2xl font-bold text-grey-900">{{ $totalBookings }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-sm text-grey-500">Confirmed</p>
            <p class="text-2xl font-bold text-success-600">{{ $confirmedBookings }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-sm text-grey-500">Pending</p>
            <p class="text-2xl font-bold text-warning-600">{{ $pendingBookings }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-sm text-grey-500">Cancelled/Failed</p>
            <p class="text-2xl font-bold text-danger-600">{{ $cancelledBookings }}</p>
        </div>
    </div>

    <!-- Bookings List -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-4 lg:px-6 py-4 border-b border-grey-200">
            <h2 class="text-lg font-semibold text-grey-900">All Bookings</h2>
            <p class="text-sm text-grey-500">Complete reservation history</p>
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
                    @forelse($bookings as $booking)
                        <tr class="hover:bg-grey-50">
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-grey-900">{{ $booking->customer->name }}</div>
                                <div class="text-xs text-grey-500">{{ $booking->customer->email }}</div>
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
        @if($bookings->hasPages())
            <div class="px-4 lg:px-6 py-4 border-t border-grey-200">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
@endsection
