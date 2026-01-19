@extends('layouts.admin')

@section('title', 'Bookings for ' . $date->formatted_date)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.bookings.index') }}" class="text-blue-600 hover:text-blue-800">
        Back to Dashboard
    </a>
</div>

<div class="bg-white shadow rounded-lg p-6 mb-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ $date->formatted_date }}</h1>
        <div class="text-right">
            <p class="text-sm text-gray-500">Total Revenue</p>
            <p class="text-2xl font-bold text-green-600">RM {{ number_format($totalRevenue, 2) }}</p>
        </div>
    </div>

    @foreach($timeSlots as $timeSlot)
        @php
            $summary = $availabilitySummary[$timeSlot->id];
            $slotBookings = $bookings->where('time_slot_id', $timeSlot->id);
        @endphp

        <div class="border rounded-lg p-4 mb-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">{{ $timeSlot->formatted_time }}</h2>
                <div class="text-right">
                    <span class="text-sm text-gray-500">
                        {{ $summary['booked_tables'] }}/{{ $summary['total_tables'] }} tables booked
                    </span>
                    <div class="text-xs text-gray-400">
                        6-seater: {{ $summary['booked_six_seaters'] }}/{{ $summary['total_six_seaters'] }} |
                        4-seater: {{ $summary['booked_four_seaters'] }}/{{ $summary['total_four_seaters'] }}
                    </div>
                </div>
            </div>

            @if($slotBookings->count() > 0)
                <table class="w-full">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="text-left py-2 px-2 text-sm font-medium text-gray-600">ID</th>
                            <th class="text-left py-2 px-2 text-sm font-medium text-gray-600">Customer</th>
                            <th class="text-left py-2 px-2 text-sm font-medium text-gray-600">Contact</th>
                            <th class="text-left py-2 px-2 text-sm font-medium text-gray-600">Pax</th>
                            <th class="text-left py-2 px-2 text-sm font-medium text-gray-600">Tables</th>
                            <th class="text-right py-2 px-2 text-sm font-medium text-gray-600">Total</th>
                            <th class="text-center py-2 px-2 text-sm font-medium text-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($slotBookings as $booking)
                            <tr class="border-b">
                                <td class="py-2 px-2">#{{ $booking->id }}</td>
                                <td class="py-2 px-2">{{ $booking->customer->name }}</td>
                                <td class="py-2 px-2">
                                    <div class="text-sm">{{ $booking->customer->email }}</div>
                                    <div class="text-xs text-gray-500">{{ $booking->customer->phone_number }}</div>
                                </td>
                                <td class="py-2 px-2">
                                    @foreach($booking->details as $detail)
                                        @if($detail->quantity > 0)
                                            <div class="text-xs">
                                                {{ $detail->price->category }}: {{ $detail->quantity }}
                                            </div>
                                        @endif
                                    @endforeach
                                </td>
                                <td class="py-2 px-2">
                                    @foreach($booking->tableBookings as $tb)
                                        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-1 rounded">
                                            {{ $tb->table->table_number }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="py-2 px-2 text-right font-medium">
                                    RM {{ number_format($booking->total, 2) }}
                                </td>
                                <td class="py-2 px-2 text-center">
                                    <a href="{{ route('admin.bookings.show', $booking) }}"
                                       class="text-blue-600 hover:text-blue-800 text-sm">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500 text-center py-4">No bookings for this time slot</p>
            @endif
        </div>
    @endforeach
</div>
@endsection
