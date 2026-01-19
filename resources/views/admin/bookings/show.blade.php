@extends('layouts.admin')

@section('title', 'Booking #' . $booking->id)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.bookings.by-date', $booking->date) }}" class="text-blue-600 hover:text-blue-800">
        Back to {{ $booking->date->formatted_date }}
    </a>
</div>

<div class="bg-white shadow rounded-lg p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Booking #{{ $booking->id }}</h1>
        <span class="px-3 py-1 rounded-full text-sm font-medium
            @if($booking->status === \App\Models\Booking::STATUS_CONFIRMED) bg-green-100 text-green-800
            @elseif($booking->status === \App\Models\Booking::STATUS_PENDING_PAYMENT) bg-yellow-100 text-yellow-800
            @elseif($booking->status === \App\Models\Booking::STATUS_PAYMENT_FAILED) bg-red-100 text-red-800
            @elseif($booking->status === \App\Models\Booking::STATUS_CANCELLED) bg-gray-100 text-gray-800
            @else bg-blue-100 text-blue-800
            @endif">
            {{ $booking->status_label }}
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Booking Details</h2>
            <dl class="space-y-2">
                <div>
                    <dt class="text-sm text-gray-600">Date</dt>
                    <dd class="font-medium">{{ $booking->date->formatted_date }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-600">Time</dt>
                    <dd class="font-medium">{{ $booking->timeSlot->formatted_time }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-600">Assigned Tables</dt>
                    <dd>
                        @foreach($booking->tableBookings as $tb)
                            <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm mr-1">
                                {{ $tb->table->table_number }} ({{ $tb->table->capacity }}-seater)
                            </span>
                        @endforeach
                    </dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-600">Created At</dt>
                    <dd class="font-medium">{{ $booking->created_at->format('d M Y, g:i A') }}</dd>
                </div>
            </dl>
        </div>

        <div>
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Customer Information</h2>
            <dl class="space-y-2">
                <div>
                    <dt class="text-sm text-gray-600">Name</dt>
                    <dd class="font-medium">{{ $booking->customer->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-600">Email</dt>
                    <dd class="font-medium">{{ $booking->customer->email }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-600">Phone</dt>
                    <dd class="font-medium">{{ $booking->customer->phone_number }}</dd>
                </div>
            </dl>
        </div>

        <div>
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Payment Information</h2>
            <dl class="space-y-2">
                @if($booking->bill_code)
                    <div>
                        <dt class="text-sm text-gray-600">Bill Code</dt>
                        <dd class="font-medium">{{ $booking->bill_code }}</dd>
                    </div>
                @endif
                @if($booking->transaction_reference_no)
                    <div>
                        <dt class="text-sm text-gray-600">Transaction Reference</dt>
                        <dd class="font-medium">{{ $booking->transaction_reference_no }}</dd>
                    </div>
                @endif
                @if($booking->transaction_time)
                    <div>
                        <dt class="text-sm text-gray-600">Transaction Time</dt>
                        <dd class="font-medium">{{ $booking->transaction_time->format('d M Y, g:i A') }}</dd>
                    </div>
                @endif
                @if($booking->status_message)
                    <div>
                        <dt class="text-sm text-gray-600">Status Message</dt>
                        <dd class="font-medium">{{ $booking->status_message }}</dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>

    <div class="mt-6 border-t pt-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Guest Breakdown</h2>
        <table class="w-full">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="text-left py-2 px-2 text-sm font-medium text-gray-600">Category</th>
                    <th class="text-center py-2 px-2 text-sm font-medium text-gray-600">Quantity</th>
                    <th class="text-right py-2 px-2 text-sm font-medium text-gray-600">Unit Price</th>
                    <th class="text-right py-2 px-2 text-sm font-medium text-gray-600">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($booking->details as $detail)
                    <tr class="border-b">
                        <td class="py-2 px-2">{{ $detail->price->category }}</td>
                        <td class="text-center py-2 px-2">{{ $detail->quantity }}</td>
                        <td class="text-right py-2 px-2">RM {{ number_format($detail->price->amount, 2) }}</td>
                        <td class="text-right py-2 px-2">RM {{ number_format($detail->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-b">
                    <td colspan="3" class="py-2 px-2 text-right text-gray-600">Subtotal:</td>
                    <td class="py-2 px-2 text-right">RM {{ number_format($booking->subtotal, 2) }}</td>
                </tr>
                <tr class="border-b">
                    <td colspan="3" class="py-2 px-2 text-right text-gray-600">Service Charge:</td>
                    <td class="py-2 px-2 text-right">RM {{ number_format($booking->service_charge, 2) }}</td>
                </tr>
                @if($booking->discount > 0)
                    <tr class="border-b">
                        <td colspan="3" class="py-2 px-2 text-right text-gray-600">Discount:</td>
                        <td class="py-2 px-2 text-right text-red-600">-RM {{ number_format($booking->discount, 2) }}</td>
                    </tr>
                @endif
                <tr>
                    <td colspan="3" class="py-2 px-2 text-right font-bold">Total:</td>
                    <td class="py-2 px-2 text-right font-bold text-lg">RM {{ number_format($booking->total, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
