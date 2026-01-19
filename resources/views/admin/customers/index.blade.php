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
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider">
                            Name
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider hidden sm:table-cell">
                            Email
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider hidden md:table-cell">
                            Phone
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-center text-xs font-medium text-grey-500 uppercase tracking-wider">
                            Bookings
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider hidden lg:table-cell">
                            Joined
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-grey-200">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-grey-50">
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-grey-900">{{ $customer->name }}</div>
                                <div class="text-xs text-grey-500 sm:hidden">{{ $customer->email }}</div>
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
