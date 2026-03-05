@extends('layouts.dashboard')

@section('title', 'Booking #' . $booking->reference_id)
@section('page-title', 'Booking #' . $booking->reference_id)
@section('page-description', 'View booking details for ' . $booking->customer->name)

@section('content')
    <div x-data="{ editStatusOpen: {{ $errors->has('transaction_reference_no') || $errors->has('status') ? 'true' : 'false' }} }" class="space-y-4">
    <div class="mb-4">
        @if(request()->query('from') === 'bookings')
            <a href="{{ route('admin.bookings.index') }}" class="inline-block px-3 py-1.5 text-sm bg-grey-600 text-white rounded-lg hover:bg-grey-700">
                Back to Bookings
            </a>
        @else
            <a href="{{ route('admin.capacity.index') }}" class="inline-block px-3 py-1.5 text-sm bg-grey-600 text-white rounded-lg hover:bg-grey-700">
                Back to Capacity
            </a>
        @endif
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
                        <a href="{{ route('admin.bookings.edit', [$booking, 'from' => request()->query('from')]) }}"
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

                <form action="{{ route('admin.bookings.resend-email', array_filter([$booking->getRouteKey(), 'from' => request()->query('from')])) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-grey-600 text-white text-sm font-medium rounded-lg hover:bg-grey-700">
                        Resend Email
                    </button>
                </form>
                <button
                    type="button"
                    @click="editStatusOpen = true"
                    class="px-4 py-2 bg-warning-600 text-white text-sm font-medium rounded-lg hover:bg-warning-700"
                >
                    Edit Booking
                </button>
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

    <!-- Edit Booking Modal -->
    <div
        x-show="editStatusOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center"
    >
        <!-- Backdrop -->
        <div
            class="absolute inset-0 bg-black/50"
            @click="editStatusOpen = false"
        ></div>

        <!-- Panel -->
        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-semibold text-grey-900">Edit Booking</h3>
                <button type="button" @click="editStatusOpen = false" class="text-grey-400 hover:text-grey-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.bookings.update-status', $booking) }}" method="POST">
                @csrf
                @method('PATCH')
                @if(request()->query('from'))
                    <input type="hidden" name="from" value="{{ request()->query('from') }}">
                @endif

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-grey-700 mb-1">Status</label>
                        <select
                            name="status"
                            class="w-full px-3 py-2 text-sm border border-grey-200 rounded-lg bg-white text-grey-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        >
                            <option value="{{ \App\Models\Booking::STATUS_INITIATED }}"      {{ $booking->status === \App\Models\Booking::STATUS_INITIATED      ? 'selected' : '' }}>Initiated</option>
                            <option value="{{ \App\Models\Booking::STATUS_PENDING_PAYMENT }}" {{ $booking->status === \App\Models\Booking::STATUS_PENDING_PAYMENT ? 'selected' : '' }}>Pending Payment</option>
                            <option value="{{ \App\Models\Booking::STATUS_CONFIRMED }}"      {{ $booking->status === \App\Models\Booking::STATUS_CONFIRMED      ? 'selected' : '' }}>Confirmed</option>
                            <option value="{{ \App\Models\Booking::STATUS_CANCELLED }}"      {{ $booking->status === \App\Models\Booking::STATUS_CANCELLED      ? 'selected' : '' }}>Cancelled</option>
                            <option value="{{ \App\Models\Booking::STATUS_PAYMENT_FAILED }}" {{ $booking->status === \App\Models\Booking::STATUS_PAYMENT_FAILED ? 'selected' : '' }}>Payment Failed</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-grey-700 mb-1">
                            Transaction Reference No
                            <span class="text-danger-600">*</span>
                        </label>
                        <input
                            type="text"
                            name="transaction_reference_no"
                            value="{{ old('transaction_reference_no', $booking->transaction_reference_no) }}"
                            required
                            placeholder="Enter transaction reference no"
                            class="w-full px-3 py-2 text-sm border border-grey-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        >
                        @error('transaction_reference_no')
                            <p class="mt-1 text-xs text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button
                        type="button"
                        @click="editStatusOpen = false"
                        class="px-4 py-2 text-sm font-medium text-grey-700 bg-grey-100 rounded-lg hover:bg-grey-200"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-warning-600 rounded-lg hover:bg-warning-700"
                    >
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    </div>{{-- /x-data --}}
@endsection
