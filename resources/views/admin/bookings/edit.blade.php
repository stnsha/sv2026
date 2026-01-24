@extends('layouts.dashboard')

@section('title', 'Amend Booking #' . $booking->id)
@section('page-title', 'Amend Booking #' . $booking->id)
@section('page-description', 'Change date and time for ' . $booking->customer->name)

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.bookings.show', $booking) }}" class="text-primary-600 hover:text-primary-700 text-sm">
            Back to Booking Details
        </a>
    </div>

    <div x-data="amendBookingForm()" class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-semibold text-grey-900 mb-4">Current Booking</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <dt class="text-sm text-grey-500">Customer</dt>
                    <dd class="font-medium text-grey-900">{{ $booking->customer->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-grey-500">Current Date</dt>
                    <dd class="font-medium text-grey-900">{{ $booking->date->formatted_date }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-grey-500">Current Time</dt>
                    <dd class="font-medium text-grey-900">{{ $booking->timeSlot->formatted_time }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-grey-500">Total Guests</dt>
                    <dd class="font-medium text-grey-900">{{ $booking->details->sum('quantity') }} pax</dd>
                </div>
                <div>
                    <dt class="text-sm text-grey-500">Current Tables</dt>
                    <dd>
                        @foreach($booking->tableBookings as $tb)
                            <span class="inline-block bg-primary-100 text-primary-700 px-2 py-1 rounded text-sm mr-1">
                                {{ $tb->table->table_number }}
                            </span>
                        @endforeach
                    </dd>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.bookings.update', $booking) }}" method="POST" @submit.prevent="confirmAmendment">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-xl font-semibold text-grey-900 mb-4">New Date & Time</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="date_id" class="block text-sm font-medium text-grey-700 mb-2">Select Date</label>
                        <div class="relative">
                            <input
                                type="text"
                                x-model="dateSearch"
                                @focus="showDateDropdown = true"
                                @click.away="showDateDropdown = false"
                                placeholder="Search for a date..."
                                class="w-full px-4 py-2 border border-grey-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            >
                            <div
                                x-show="showDateDropdown"
                                x-cloak
                                class="absolute z-10 w-full mt-1 bg-white border border-grey-200 rounded-lg shadow-lg max-h-60 overflow-y-auto"
                            >
                                <template x-for="date in filteredDates" :key="date.id">
                                    <button
                                        type="button"
                                        @click="selectDate(date)"
                                        class="w-full px-4 py-2 text-left hover:bg-grey-50 focus:bg-grey-50"
                                        :class="{ 'bg-primary-50': selectedDateId == date.id }"
                                    >
                                        <span x-text="date.formatted_date"></span>
                                    </button>
                                </template>
                                <div x-show="filteredDates.length === 0" class="px-4 py-2 text-grey-500 text-sm">
                                    No dates found
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="date_id" :value="selectedDateId">
                        @error('date_id')
                            <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-grey-700 mb-2">Select Time Slot</label>
                        <div class="space-y-2">
                            @foreach($timeSlots as $timeSlot)
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer transition-all"
                                       :class="selectedTimeSlotId == {{ $timeSlot->id }} ? 'border-primary-500 bg-primary-50' : 'border-grey-200 hover:border-grey-300'">
                                    <input
                                        type="radio"
                                        name="time_slot_id"
                                        value="{{ $timeSlot->id }}"
                                        x-model="selectedTimeSlotId"
                                        @change="checkAvailability"
                                        class="sr-only"
                                    >
                                    <span class="text-grey-900">{{ $timeSlot->formatted_time }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('time_slot_id')
                            <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div x-show="isChecking" class="mt-4">
                    <div class="flex items-center text-grey-600">
                        <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Checking availability...
                    </div>
                </div>

                <div x-show="availabilityResult && !isChecking" class="mt-4">
                    <div x-show="availabilityResult?.available" class="p-4 bg-success-50 border border-success-200 rounded-lg">
                        <div class="flex items-center text-success-700">
                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span x-text="availabilityResult?.message"></span>
                        </div>
                    </div>
                    <div x-show="!availabilityResult?.available" class="p-4 bg-danger-50 border border-danger-200 rounded-lg">
                        <div class="flex items-center text-danger-700">
                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <span x-text="availabilityResult?.message"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('admin.bookings.show', $booking) }}"
                   class="px-4 py-2 border border-grey-300 text-grey-700 rounded-lg hover:bg-grey-50">
                    Cancel
                </a>
                <button
                    type="submit"
                    :disabled="!canSubmit"
                    class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Amend Booking
                </button>
            </div>

            <div
                x-show="showConfirmModal"
                x-cloak
                class="fixed inset-0 z-50 overflow-y-auto"
                aria-labelledby="modal-title"
                role="dialog"
                aria-modal="true"
            >
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div
                        x-show="showConfirmModal"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed inset-0 bg-grey-500 bg-opacity-75 transition-opacity"
                        @click="showConfirmModal = false"
                    ></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div
                        x-show="showConfirmModal"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                    >
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-warning-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-warning-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg leading-6 font-medium text-grey-900" id="modal-title">
                                        Confirm Amendment
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-grey-500">
                                            You are about to change this booking:
                                        </p>
                                        <div class="mt-3 space-y-2 text-sm">
                                            <p><strong>From:</strong> {{ $booking->date->formatted_date }} - {{ $booking->timeSlot->formatted_time }}</p>
                                            <p><strong>To:</strong> <span x-text="selectedDateText"></span> - <span x-text="selectedTimeSlotText"></span></p>
                                        </div>
                                        <p class="mt-3 text-sm text-grey-500">
                                            This will reassign tables for this booking. Continue?
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-grey-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button
                                type="button"
                                @click="submitForm"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                Confirm Amendment
                            </button>
                            <button
                                type="button"
                                @click="showConfirmModal = false"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-grey-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-grey-700 hover:bg-grey-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function amendBookingForm() {
            return {
                dates: @json($dates->map(fn($d) => ['id' => $d->id, 'formatted_date' => $d->formatted_date, 'date_value' => $d->date_value->format('Y-m-d')])),
                timeSlots: @json($timeSlots->map(fn($t) => ['id' => $t->id, 'formatted_time' => $t->formatted_time])),
                selectedDateId: {{ $booking->date_id }},
                selectedTimeSlotId: '{{ $booking->time_slot_id }}',
                dateSearch: '{{ $booking->date->formatted_date }}',
                showDateDropdown: false,
                isChecking: false,
                availabilityResult: null,
                showConfirmModal: false,
                originalDateId: {{ $booking->date_id }},
                originalTimeSlotId: {{ $booking->time_slot_id }},

                get filteredDates() {
                    if (!this.dateSearch) return this.dates;
                    const search = this.dateSearch.toLowerCase();
                    return this.dates.filter(date =>
                        date.formatted_date.toLowerCase().includes(search) ||
                        date.date_value.includes(search)
                    );
                },

                get selectedDateText() {
                    const date = this.dates.find(d => d.id == this.selectedDateId);
                    return date ? date.formatted_date : '';
                },

                get selectedTimeSlotText() {
                    const slot = this.timeSlots.find(t => t.id == this.selectedTimeSlotId);
                    return slot ? slot.formatted_time : '';
                },

                get hasChanged() {
                    return this.selectedDateId != this.originalDateId ||
                           this.selectedTimeSlotId != this.originalTimeSlotId;
                },

                get canSubmit() {
                    return this.selectedDateId &&
                           this.selectedTimeSlotId &&
                           this.hasChanged &&
                           this.availabilityResult?.available &&
                           !this.isChecking;
                },

                selectDate(date) {
                    this.selectedDateId = date.id;
                    this.dateSearch = date.formatted_date;
                    this.showDateDropdown = false;
                    this.checkAvailability();
                },

                async checkAvailability() {
                    if (!this.selectedDateId || !this.selectedTimeSlotId) return;

                    this.isChecking = true;
                    this.availabilityResult = null;

                    try {
                        const response = await fetch('{{ route('admin.bookings.check-amendment-availability', $booking) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                date_id: this.selectedDateId,
                                time_slot_id: this.selectedTimeSlotId,
                            }),
                        });

                        this.availabilityResult = await response.json();
                    } catch (error) {
                        this.availabilityResult = {
                            available: false,
                            message: 'Failed to check availability. Please try again.',
                        };
                    } finally {
                        this.isChecking = false;
                    }
                },

                confirmAmendment() {
                    if (!this.canSubmit) return;
                    this.showConfirmModal = true;
                },

                submitForm() {
                    this.$el.closest('form').submit();
                },

                init() {
                    this.checkAvailability();
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
@endsection
