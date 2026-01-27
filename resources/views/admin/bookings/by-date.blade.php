@extends('layouts.dashboard')

@section('title', 'Bookings for ' . $date->formatted_date)
@section('page-title')
    {{ $date->formatted_date }}
    <span class="block text-lg font-medium text-grey-500">Slot {{ $timeSlot->id }}: {{ $timeSlot->formatted_time }}</span>
@endsection
@section('page-description', 'View bookings for this time slot')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.capacity.index') }}" class="inline-block px-3 py-1.5 text-sm bg-grey-600 text-white rounded-lg hover:bg-grey-700">
            Back to Capacity
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-xl font-semibold text-grey-900">Bookings Overview</h2>
                <p class="text-sm text-grey-500 mt-1">
                    {{ $availabilitySummary['booked_tables'] }}/{{ $availabilitySummary['total_tables'] }} tables booked
                    <span class="mx-2">|</span>
                    6-seater: {{ $availabilitySummary['booked_six_seaters'] }}/{{ $availabilitySummary['total_six_seaters'] }}
                    <span class="mx-2">|</span>
                    4-seater: {{ $availabilitySummary['booked_four_seaters'] }}/{{ $availabilitySummary['total_four_seaters'] }}
                </p>
            </div>
            <div class="text-right">
                <p class="text-sm text-grey-500">Total Revenue</p>
                <p class="text-2xl font-bold text-success-600">RM {{ number_format($totalRevenue, 2) }}</p>
            </div>
        </div>

        @if($bookings->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-grey-200">
                    <thead class="bg-grey-50">
                        <tr>
                            <th class="text-left py-3 px-4 text-xs font-medium text-grey-500 uppercase tracking-wider">ID</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-grey-500 uppercase tracking-wider">Customer</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-grey-500 uppercase tracking-wider">Contact</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-grey-500 uppercase tracking-wider">Pax</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-grey-500 uppercase tracking-wider">Tables</th>
                            <th class="text-right py-3 px-4 text-xs font-medium text-grey-500 uppercase tracking-wider">Total</th>
                            <th class="text-center py-3 px-4 text-xs font-medium text-grey-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-grey-200">
                        @foreach($bookings as $booking)
                            <tr class="hover:bg-grey-50">
                                <td class="py-3 px-4 text-sm text-grey-900">#{{ $booking->reference_id }}</td>
                                <td class="py-3 px-4 text-sm font-medium text-grey-900">{{ $booking->customer->name }}</td>
                                <td class="py-3 px-4">
                                    <div class="text-sm text-grey-900">{{ $booking->customer->email }}</div>
                                    <div class="text-xs text-grey-500">{{ $booking->customer->phone_number }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    @foreach($booking->details as $detail)
                                        @if($detail->quantity > 0)
                                            <div class="text-xs text-grey-600">
                                                {{ $detail->price->category }}: {{ $detail->quantity }}
                                            </div>
                                        @endif
                                    @endforeach
                                </td>
                                <td class="py-3 px-4">
                                    @foreach($booking->tableBookings as $tb)
                                        <span class="inline-block bg-primary-100 text-primary-700 text-xs px-2 py-1 rounded mr-1">
                                            {{ $tb->table->table_number }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="py-3 px-4 text-right text-sm font-medium text-grey-900">
                                    RM {{ number_format($booking->total, 2) }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <a href="{{ route('admin.bookings.show', ['booking' => $booking, 'from' => request()->query('from')]) }}"
                                       class="px-3 py-1.5 text-sm bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-grey-500 text-center py-8">No bookings for this time slot</p>
        @endif
    </div>
@endsection
