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
                <p class="text-sm text-grey-500">Capacity overview by date and time slot</p>
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
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-grey-800 uppercase tracking-wider">
                            Date
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-grey-800 uppercase tracking-wider">
                            Time Slot
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-center text-xs font-medium text-success-600 uppercase tracking-wider">
                            Available
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-center text-xs font-medium text-primary-600 uppercase tracking-wider">
                            Booked
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-center text-xs font-medium text-danger-600 uppercase tracking-wider">
                            Blocked
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-right text-xs font-medium text-grey-800 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-grey-200">
                    @forelse($dates as $date)
                        @foreach($timeSlots as $index => $timeSlot)
                            @php $summary = $dateSummaries[$date->id][$timeSlot->id]; @endphp
                            <tr class="hover:bg-grey-50 {{ $index > 0 ? 'border-t border-grey-100' : '' }}">
                                @if($index === 0)
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap align-top" rowspan="{{ $timeSlots->count() }}">
                                        <div class="text-sm font-medium text-grey-900">
                                            {{ $date->date_value->format('l') }}
                                        </div>
                                        <div class="text-xs text-grey-500">
                                            {{ $date->formatted_date }}
                                        </div>
                                    </td>
                                @endif
                                <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-grey-900">
                                        {{ $timeSlot->start_time->format('g:i A') }}
                                    </div>
                                    <div class="text-xs text-grey-500">
                                        {{ $timeSlot->end_time->format('g:i A') }}
                                    </div>
                                </td>
                                <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm font-medium text-success-600">{{ $summary['available_tables'] }}</span>
                                </td>
                                <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm font-medium text-primary-600">{{ $summary['confirmed_tables'] }}</span>
                                </td>
                                <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm font-medium text-danger-600">{{ $summary['blocked_tables'] }}</span>
                                </td>
                                <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.bookings.by-date', ['date' => $date, 'timeSlot' => $timeSlot, 'from' => 'capacity']) }}"
                                           class="px-3 py-1.5 text-sm bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                                            View Bookings
                                        </a>
                                        <a href="{{ route('admin.capacity.edit', ['date' => $date, 'timeSlot' => $timeSlot]) }}"
                                           class="px-3 py-1.5 text-sm bg-grey-600 text-white rounded-lg hover:bg-grey-700">
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 lg:px-6 py-8 text-center text-grey-500">
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
