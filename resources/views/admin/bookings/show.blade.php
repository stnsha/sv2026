@extends('layouts.dashboard')

@section('title', 'Booking #' . $booking->reference_id)
@section('page-title', 'Booking #' . $booking->reference_id)
@section('page-description', 'View booking details for ' . $booking->customer->name)

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.capacity.index') }}" class="inline-block px-3 py-1.5 text-sm bg-grey-600 text-white rounded-lg hover:bg-grey-700">
            Back to Capacity
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-grey-900">Booking Details</h2>
            <div class="flex items-center gap-3">
                @switch($booking->status)
                    @case(\App\Models\Booking::STATUS_CONFIRMED)
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-success-100 text-success-700">
                            {{ $booking->status_label }}
                        </span>
                        <a href="{{ route('admin.bookings.edit', $booking) }}"
                           class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700">
                            Amend Booking
                        </a>
                        @break
                    @case(\App\Models\Booking::STATUS_PENDING_PAYMENT)
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-warning-100 text-warning-700">
                            {{ $booking->status_label }}
                        </span>
                        @break
                    @case(\App\Models\Booking::STATUS_PAYMENT_FAILED)
                    @case(\App\Models\Booking::STATUS_CANCELLED)
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-danger-100 text-danger-700">
                            {{ $booking->status_label }}
                        </span>
                        @break
                    @default
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-grey-100 text-grey-700">
                            {{ $booking->status_label }}
                        </span>
                @endswitch
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <h3 class="text-lg font-semibold text-grey-800 mb-4">Reservation Info</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm text-grey-500">Date</dt>
                        <dd class="font-medium text-grey-900">{{ $booking->date->formatted_date }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-grey-500">Time</dt>
                        <dd class="font-medium text-grey-900">{{ $booking->timeSlot->formatted_time }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-grey-500">Assigned Tables</dt>
                        <dd>
                            @foreach($booking->tableBookings as $tb)
                                <span class="inline-block bg-primary-100 text-primary-700 px-2 py-1 rounded text-sm mr-1 mb-1">
                                    {{ $tb->table->table_number }} ({{ $tb->table->capacity }}-seater)
                                </span>
                            @endforeach
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-grey-500">Created At</dt>
                        <dd class="font-medium text-grey-900">{{ $booking->created_at->format('d M Y, g:i A') }}</dd>
                    </div>
                </dl>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-grey-800 mb-4">Customer Information</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm text-grey-500">Name</dt>
                        <dd class="font-medium text-grey-900">{{ $booking->customer->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-grey-500">Email</dt>
                        <dd class="font-medium text-grey-900">{{ $booking->customer->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-grey-500">Phone</dt>
                        <dd class="font-medium text-grey-900">{{ $booking->customer->phone_number }}</dd>
                    </div>
                </dl>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-grey-800 mb-4">Payment Information</h3>
                <dl class="space-y-3">
                    @if($booking->bill_code)
                        <div>
                            <dt class="text-sm text-grey-500">Bill Code</dt>
                            <dd class="font-medium text-grey-900">{{ $booking->bill_code }}</dd>
                        </div>
                    @endif
                    @if($booking->transaction_reference_no)
                        <div>
                            <dt class="text-sm text-grey-500">Transaction Reference</dt>
                            <dd class="font-medium text-grey-900">{{ $booking->transaction_reference_no }}</dd>
                        </div>
                    @endif
                    @if($booking->transaction_time)
                        <div>
                            <dt class="text-sm text-grey-500">Transaction Time</dt>
                            <dd class="font-medium text-grey-900">{{ $booking->transaction_time->format('d M Y, g:i A') }}</dd>
                        </div>
                    @endif
                    @if($booking->status_message)
                        <div>
                            <dt class="text-sm text-grey-500">Status Message</dt>
                            <dd class="font-medium text-grey-900">{{ $booking->status_message }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>

        <div class="mt-6 border-t border-grey-200 pt-6">
            <h3 class="text-lg font-semibold text-grey-800 mb-4">Guest Breakdown</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-grey-200">
                    <thead class="bg-grey-50">
                        <tr>
                            <th class="text-left py-3 px-4 text-xs font-medium text-grey-500 uppercase tracking-wider">Category</th>
                            <th class="text-center py-3 px-4 text-xs font-medium text-grey-500 uppercase tracking-wider">Quantity</th>
                            <th class="text-right py-3 px-4 text-xs font-medium text-grey-500 uppercase tracking-wider">Unit Price</th>
                            <th class="text-right py-3 px-4 text-xs font-medium text-grey-500 uppercase tracking-wider">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-grey-200">
                        @foreach($booking->details as $detail)
                            <tr>
                                <td class="py-3 px-4 text-sm text-grey-900">{{ $detail->price->category }}</td>
                                <td class="text-center py-3 px-4 text-sm text-grey-600">{{ $detail->quantity }}</td>
                                <td class="text-right py-3 px-4 text-sm text-grey-600">RM {{ number_format($detail->price->amount, 2) }}</td>
                                <td class="text-right py-3 px-4 text-sm text-grey-900">RM {{ number_format($detail->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-grey-50">
                        <tr>
                            <td colspan="3" class="py-3 px-4 text-right text-sm text-grey-500">Subtotal:</td>
                            <td class="py-3 px-4 text-right text-sm text-grey-900">RM {{ number_format($booking->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="py-3 px-4 text-right text-sm text-grey-500">Service Charge:</td>
                            <td class="py-3 px-4 text-right text-sm text-grey-900">RM {{ number_format($booking->service_charge, 2) }}</td>
                        </tr>
                        @if($booking->discount > 0)
                            <tr>
                                <td colspan="3" class="py-3 px-4 text-right text-sm text-grey-500">Discount:</td>
                                <td class="py-3 px-4 text-right text-sm text-danger-600">-RM {{ number_format($booking->discount, 2) }}</td>
                            </tr>
                        @endif
                        <tr class="border-t-2 border-grey-300">
                            <td colspan="3" class="py-3 px-4 text-right font-semibold text-grey-900">Total:</td>
                            <td class="py-3 px-4 text-right font-bold text-lg text-grey-900">RM {{ number_format($booking->total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
