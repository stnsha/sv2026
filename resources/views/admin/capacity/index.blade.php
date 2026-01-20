@extends('layouts.dashboard')

@section('title', 'Capacity')
@section('page-title', 'Capacity')
@section('page-description', 'View and manage table availability by date')

@section('content')
    <!-- Summary Cards -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-sm text-grey-500">Total Tables</p>
            <p class="text-2xl font-bold text-grey-900">{{ $totalTables }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-sm text-grey-500">Total Capacity</p>
            <p class="text-2xl font-bold text-grey-900">{{ $totalCapacity }} pax</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-success-100 border border-success-200 text-success-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Capacity List -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-4 lg:px-6 py-4 border-b border-grey-200 flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold text-grey-900">Dates</h2>
                <p class="text-sm text-grey-500">Capacity overview by date</p>
            </div>
            <div>
                <a href="{{ route('admin.capacity.index', ['sort' => $sortDirection === 'asc' ? 'desc' : 'asc']) }}"
                   class="text-sm text-primary-600 hover:text-primary-700 flex items-center gap-1">
                    Sort by Date
                    @if($sortDirection === 'asc')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                        </svg>
                    @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    @endif
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-grey-200">
                <thead class="bg-grey-50">
                    <tr>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider">
                            Date
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider">
                            Available
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider hidden md:table-cell">
                            Confirmed
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider hidden sm:table-cell">
                            Blocked
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-right text-xs font-medium text-grey-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-grey-200">
                    @forelse($dates as $date)
                        @php $summary = $dateSummaries[$date->id]; @endphp
                        <tr class="hover:bg-grey-50">
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-grey-900">
                                    {{ $date->date_value->format('l') }}
                                </div>
                                <div class="text-xs text-grey-500">
                                    {{ $date->formatted_date }}
                                </div>
                            </td>
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-success-600">
                                    {{ $summary['available_tables'] }} tables
                                </div>
                                <div class="text-xs text-grey-500">
                                    {{ $summary['available_capacity'] }} pax
                                </div>
                            </td>
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                @if($summary['confirmed_tables'] > 0)
                                    <div class="text-sm font-medium text-primary-600">
                                        {{ $summary['confirmed_tables'] }} tables
                                    </div>
                                    <div class="text-xs text-grey-500">
                                        {{ $summary['confirmed_capacity'] }} pax
                                    </div>
                                @else
                                    <span class="text-sm text-grey-400">None</span>
                                @endif
                            </td>
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                @if($summary['blocked_tables'] > 0)
                                    <div class="text-sm font-medium text-danger-600">
                                        {{ $summary['blocked_tables'] }} tables
                                    </div>
                                    <div class="text-xs text-grey-500">
                                        {{ $summary['blocked_capacity'] }} pax
                                    </div>
                                @else
                                    <span class="text-sm text-grey-400">None</span>
                                @endif
                            </td>
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.bookings.by-date', $date) }}"
                                   class="text-primary-600 hover:text-primary-700 mr-3">
                                    View Bookings
                                </a>
                                <a href="{{ route('admin.capacity.edit', $date) }}"
                                   class="text-grey-600 hover:text-grey-700">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 lg:px-6 py-8 text-center text-grey-500">
                                No dates found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($dates->hasPages())
            <div class="px-4 lg:px-6 py-4 border-t border-grey-200">
                {{ $dates->links() }}
            </div>
        @endif
    </div>
@endsection
