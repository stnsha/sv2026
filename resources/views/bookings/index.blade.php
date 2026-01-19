@extends('layouts.app')

@section('title', 'Book a Table')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Book a Table</h1>

    <form id="bookingForm" action="{{ route('booking.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Select Date & Time</h2>

                <div class="mb-4">
                    <label for="date_id" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <select name="date_id" id="date_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Select a date</option>
                        @foreach($dates as $date)
                            <option value="{{ $date->id }}" {{ old('date_id') == $date->id ? 'selected' : '' }}>
                                {{ $date->formatted_date }}
                            </option>
                        @endforeach
                    </select>
                    @error('date_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="time_slot_id" class="block text-sm font-medium text-gray-700 mb-1">Time Slot</label>
                    <select name="time_slot_id" id="time_slot_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Select a time slot</option>
                        @foreach($timeSlots as $timeSlot)
                            <option value="{{ $timeSlot->id }}" {{ old('time_slot_id') == $timeSlot->id ? 'selected' : '' }}>
                                {{ $timeSlot->formatted_time }}
                            </option>
                        @endforeach
                    </select>
                    @error('time_slot_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div id="availabilityStatus" class="mb-4 p-3 rounded hidden">
                </div>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Number of Guests</h2>

                @foreach($prices as $index => $price)
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            {{ $price->category }} (RM {{ number_format($price->amount, 2) }})
                        </label>
                        <input type="hidden" name="pax_details[{{ $index }}][price_id]" value="{{ $price->id }}">
                        <input type="number" name="pax_details[{{ $index }}][quantity]" id="pax_{{ $price->id }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 pax-input"
                               data-price="{{ $price->amount }}"
                               value="{{ old('pax_details.' . $index . '.quantity', 0) }}" min="0">
                    </div>
                @endforeach

                <div class="mt-4 p-4 bg-gray-50 rounded-md">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Total Guests:</span>
                        <span id="totalPax" class="font-semibold">0</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Subtotal:</span>
                        <span id="subtotal">RM 0.00</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Service Charge:</span>
                        <span>RM 1.00</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total:</span>
                        <span id="total">RM 1.00</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 border-t pt-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Customer Details</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="customer[name]" id="customer_name"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('customer.name') }}" required>
                    @error('customer.name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="customer[email]" id="customer_email"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('customer.email') }}" required>
                    @error('customer.email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="tel" name="customer[phone_number]" id="customer_phone"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('customer.phone_number') }}" required>
                    @error('customer.phone_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" id="submitBtn"
                    class="w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:bg-gray-400 disabled:cursor-not-allowed">
                Proceed to Payment
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateSelect = document.getElementById('date_id');
    const timeSlotSelect = document.getElementById('time_slot_id');
    const paxInputs = document.querySelectorAll('.pax-input');
    const availabilityStatus = document.getElementById('availabilityStatus');
    const totalPaxEl = document.getElementById('totalPax');
    const subtotalEl = document.getElementById('subtotal');
    const totalEl = document.getElementById('total');
    const submitBtn = document.getElementById('submitBtn');

    function calculateTotals() {
        let totalPax = 0;
        let subtotal = 0;

        paxInputs.forEach(input => {
            const qty = parseInt(input.value) || 0;
            const price = parseFloat(input.dataset.price);
            totalPax += qty;
            subtotal += qty * price;
        });

        totalPaxEl.textContent = totalPax;
        subtotalEl.textContent = 'RM ' + subtotal.toFixed(2);
        totalEl.textContent = 'RM ' + (subtotal + 1).toFixed(2);

        return totalPax;
    }

    function checkAvailability() {
        const dateId = dateSelect.value;
        const timeSlotId = timeSlotSelect.value;
        const totalPax = calculateTotals();

        if (!dateId || !timeSlotId || totalPax < 1) {
            availabilityStatus.classList.add('hidden');
            submitBtn.disabled = true;
            return;
        }

        fetch('{{ route('booking.check-availability') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                date_id: dateId,
                time_slot_id: timeSlotId,
                total_pax: totalPax
            })
        })
        .then(response => response.json())
        .then(data => {
            availabilityStatus.classList.remove('hidden', 'bg-green-100', 'bg-red-100', 'text-green-700', 'text-red-700');

            if (data.available) {
                availabilityStatus.classList.add('bg-green-100', 'text-green-700');
                availabilityStatus.innerHTML = 'Tables available! ' +
                    (data.tables_needed.six_seaters > 0 ? data.tables_needed.six_seaters + ' x 6-seater ' : '') +
                    (data.tables_needed.four_seaters > 0 ? data.tables_needed.four_seaters + ' x 4-seater' : '');
                submitBtn.disabled = false;
            } else {
                availabilityStatus.classList.add('bg-red-100', 'text-red-700');
                availabilityStatus.textContent = 'Not enough tables available for ' + totalPax + ' guests.';
                submitBtn.disabled = true;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            availabilityStatus.classList.add('hidden');
        });
    }

    dateSelect.addEventListener('change', checkAvailability);
    timeSlotSelect.addEventListener('change', checkAvailability);
    paxInputs.forEach(input => {
        input.addEventListener('input', function() {
            calculateTotals();
            checkAvailability();
        });
    });

    calculateTotals();
});
</script>
@endpush
