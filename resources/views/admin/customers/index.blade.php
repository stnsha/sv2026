@extends('layouts.dashboard')

@section('title', 'Customers')
@section('page-title', 'Customers')
@section('page-description', 'View and manage your customer database')

@section('content')
    <!-- Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-sm text-grey-500">Total Customers</p>
            <p class="text-2xl font-bold text-grey-900">{{ $totalCustomers }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-sm text-grey-500">New This Month</p>
            <p class="text-2xl font-bold text-success-600">{{ $newThisMonth }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 col-span-2 lg:col-span-1">
            <p class="text-sm text-grey-500">Total Bookings</p>
            <p class="text-2xl font-bold text-secondary-500">{{ $totalBookingsCount }}</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <x-table-filter
        :route="route('admin.customers.index')"
        :filters="[]"
        searchPlaceholder="Search name, email or phone..."
    />

    <!-- Customers List -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-4 lg:px-6 py-4 border-b border-grey-200">
            <h2 class="text-lg font-semibold text-grey-900">All Customers</h2>
            <p class="text-sm text-grey-500">Customer contact information and booking history</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-grey-200">
                <thead class="bg-grey-50">
                    <tr>
                        <th class="px-4 lg:px-6 py-3 text-left">
                            <x-sortable-header
                                column="name"
                                label="Name"
                                :currentSort="$currentSort"
                                :currentDirection="$currentDirection"
                            />
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-left hidden sm:table-cell">
                            <x-sortable-header
                                column="email"
                                label="Email"
                                :currentSort="$currentSort"
                                :currentDirection="$currentDirection"
                            />
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider hidden md:table-cell">
                            Phone
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-center">
                            <x-sortable-header
                                column="bookings_count"
                                label="Bookings"
                                :currentSort="$currentSort"
                                :currentDirection="$currentDirection"
                                class="justify-center"
                            />
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-left hidden lg:table-cell">
                            <x-sortable-header
                                column="created_at"
                                label="Joined"
                                :currentSort="$currentSort"
                                :currentDirection="$currentDirection"
                            />
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-grey-200" x-data="{ expandedId: null }">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-grey-50 cursor-pointer"
                            @click="expandedId = expandedId === {{ $customer->id }} ? null : {{ $customer->id }}">
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-grey-400 transition-transform duration-200"
                                         :class="expandedId === {{ $customer->id }} ? 'rotate-90' : ''"
                                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                    </svg>
                                    <div>
                                        <div class="text-sm font-medium text-grey-900">{{ $customer->name }}</div>
                                        <div class="text-xs text-grey-500 sm:hidden">{{ $customer->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-grey-600 hidden sm:table-cell">
                                {{ $customer->email }}
                            </td>
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-grey-600 hidden md:table-cell">
                                {{ $customer->phone_number ?? '-' }}
                            </td>
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-secondary-100 text-primary-600">
                                    {{ $customer->bookings_count }}
                                </span>
                            </td>
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-grey-600 hidden lg:table-cell">
                                {{ $customer->created_at->format('d M Y') }}
                            </td>
                        </tr>
                        <tr x-show="expandedId === {{ $customer->id }}" x-cloak>
                            <td colspan="5" class="px-4 lg:px-6 py-4 bg-grey-50">
                                @if($customer->bookings->isEmpty())
                                    <p class="text-sm text-grey-500 text-center py-2">No bookings found for this customer.</p>
                                @else
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-grey-200 text-sm">
                                            <thead>
                                                <tr>
                                                    <th class="px-3 py-2 text-left text-xs font-medium text-grey-500 uppercase">Reference</th>
                                                    <th class="px-3 py-2 text-left text-xs font-medium text-grey-500 uppercase">Date</th>
                                                    <th class="px-3 py-2 text-left text-xs font-medium text-grey-500 uppercase hidden md:table-cell">Time Slot</th>
                                                    <th class="px-3 py-2 text-left text-xs font-medium text-grey-500 uppercase">Status</th>
                                                    <th class="px-3 py-2 text-right text-xs font-medium text-grey-500 uppercase">Total</th>
                                                    <th class="px-3 py-2 text-right text-xs font-medium text-grey-500 uppercase"></th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-grey-100">
                                                @foreach($customer->bookings as $booking)
                                                    <tr>
                                                        <td class="px-3 py-2 text-grey-700 font-mono">{{ $booking->reference_id }}</td>
                                                        <td class="px-3 py-2 text-grey-600">{{ $booking->date->formatted_date ?? '-' }}</td>
                                                        <td class="px-3 py-2 text-grey-600 hidden md:table-cell">{{ $booking->timeSlot->formatted_time ?? '-' }}</td>
                                                        <td class="px-3 py-2">
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
                                                        <td class="px-3 py-2 text-grey-600 text-right">RM {{ number_format($booking->total, 2) }}</td>
                                                        <td class="px-3 py-2 text-right">
                                                            <a href="{{ route('admin.bookings.show', ['booking' => $booking, 'from' => 'customers']) }}"
                                                               @click.stop
                                                               class="px-3 py-1.5 text-sm bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                                                                View
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 lg:px-6 py-8 text-center text-grey-500">
                                No customers found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($customers->hasPages())
            <div class="px-4 lg:px-6 py-4 border-t border-grey-200">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
@endsection

@push('styles')
<style>[x-cloak] { display: none !important; }</style>
@endpush
