@extends('layouts.dashboard')

@section('title', 'Amend Booking #' . $booking->reference_id)
@section('page-title', 'Amend Booking #' . $booking->reference_id)
@section('page-description', 'Change date, time, or tables for ' . $booking->customer->name)

@section('content')
    <div x-data="amendBookingForm()" class="space-y-6">
        <div class="flex justify-between items-center">
            <a href="{{ route('admin.capacity.index') }}" class="px-3 py-1.5 text-sm bg-grey-600 text-white rounded-lg hover:bg-grey-700">
                Back to Capacity
            </a>
            <div class="flex gap-3">
                <a href="{{ route('admin.bookings.show', $booking) }}"
                   class="px-4 py-2 bg-grey-600 text-white rounded-lg hover:bg-grey-700">
                    Cancel
                </a>
                <button
                    type="button"
                    @click="confirmAmendment"
                    :disabled="!canSubmit"
                    class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Amend Booking
                </button>
            </div>
        </div>
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
                                {{ $tb->table->table_number }} ({{ $tb->table->capacity }}-seater)
                            </span>
                        @endforeach
                    </dd>
                </div>
            </div>
        </div>

        <form x-ref="amendForm" action="{{ route('admin.bookings.update', $booking) }}" method="POST" @submit.prevent="confirmAmendment">
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
                                @focus="onDateFocus"
                                @blur="onDateBlur"
                                @click.away="showDateDropdown = false"
                                placeholder="Type to search dates..."
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
                                        x-model.number="selectedTimeSlotId"
                                        @change="onSlotChange"
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

                <div x-show="availabilityResult !== null && !isChecking && availabilityResult?.available === false" x-cloak class="mt-4">
                    <div class="p-4 bg-danger-50 border border-danger-200 rounded-lg">
                        <div class="flex items-center text-danger-700">
                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <span x-text="availabilityResult?.message"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="availabilityResult?.available || availableTables.length > 0" x-cloak class="bg-white rounded-xl shadow-sm p-6 mt-6">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h2 class="text-xl font-semibold text-grey-900">Table Selection</h2>
                        <p class="text-sm text-grey-500 mt-1">
                            Select tables for this booking. Required capacity: <strong x-text="requiredPax"></strong> pax
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-grey-500">Selected Capacity</div>
                        <div class="text-lg font-semibold" :class="selectedCapacity >= requiredPax ? 'text-success-600' : 'text-danger-600'">
                            <span x-text="selectedCapacity"></span> / <span x-text="requiredPax"></span> pax
                        </div>
                    </div>
                </div>

                <div class="mb-4 flex flex-wrap gap-2 text-sm">
                    <span class="inline-flex items-center">
                        <span class="w-4 h-4 rounded bg-primary-500 mr-1"></span> Selected
                    </span>
                    <span class="inline-flex items-center">
                        <span class="w-4 h-4 rounded bg-secondary-500 mr-1"></span> Current
                    </span>
                    <span class="inline-flex items-center">
                        <span class="w-4 h-4 rounded bg-success-500 mr-1"></span> Available
                    </span>
                    <span class="inline-flex items-center">
                        <span class="w-4 h-4 rounded bg-grey-300 mr-1"></span> Unavailable
                    </span>
                </div>

                <div x-show="selectedCapacity < requiredPax && selectedTableIds.length > 0" class="mb-4 p-3 bg-warning-50 border border-warning-200 rounded-lg text-warning-700 text-sm">
                    Insufficient capacity. Please select more tables to meet the required <span x-text="requiredPax"></span> pax.
                </div>

                <div class="mb-6">
                    <h3 class="text-md font-medium text-grey-700 mb-3">6-Seater Tables</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        <template x-for="table in allTables.filter(t => t.capacity === 6)" :key="table.id">
                            <button
                                type="button"
                                @click="toggleTable(table)"
                                :disabled="!isTableAvailable(table.id)"
                                class="relative flex flex-col items-center p-4 border-2 rounded-lg transition-all"
                                :class="getTableClasses(table)"
                            >
                                <div class="text-lg font-bold text-grey-900" x-text="table.table_number"></div>
                                <div class="text-sm text-grey-500" x-text="table.capacity + '-seater'"></div>
                                <div class="mt-2">
                                    <span
                                        class="px-2 py-1 text-xs rounded-full"
                                        :class="getTableBadgeClasses(table)"
                                        x-text="getTableBadgeText(table)"
                                    ></span>
                                </div>
                                <input
                                    type="checkbox"
                                    :name="'table_ids[]'"
                                    :value="table.id"
                                    :checked="selectedTableIds.includes(table.id)"
                                    class="sr-only"
                                >
                            </button>
                        </template>
                    </div>
                </div>

                <div>
                    <h3 class="text-md font-medium text-grey-700 mb-3">4-Seater Tables</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        <template x-for="table in allTables.filter(t => t.capacity === 4)" :key="table.id">
                            <button
                                type="button"
                                @click="toggleTable(table)"
                                :disabled="!isTableAvailable(table.id)"
                                class="relative flex flex-col items-center p-4 border-2 rounded-lg transition-all"
                                :class="getTableClasses(table)"
                            >
                                <div class="text-lg font-bold text-grey-900" x-text="table.table_number"></div>
                                <div class="text-sm text-grey-500" x-text="table.capacity + '-seater'"></div>
                                <div class="mt-2">
                                    <span
                                        class="px-2 py-1 text-xs rounded-full"
                                        :class="getTableBadgeClasses(table)"
                                        x-text="getTableBadgeText(table)"
                                    ></span>
                                </div>
                                <input
                                    type="checkbox"
                                    :name="'table_ids[]'"
                                    :value="table.id"
                                    :checked="selectedTableIds.includes(table.id)"
                                    class="sr-only"
                                >
                            </button>
                        </template>
                    </div>
                </div>

                @error('table_ids')
                    <p class="mt-4 text-sm text-danger-600">{{ $message }}</p>
                @enderror
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
                        @click.stop
                        class="relative z-10 inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
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
                                        <div class="mt-3 space-y-3 text-sm">
                                            <div class="p-3 bg-grey-50 rounded-lg">
                                                <p class="font-medium text-grey-700 mb-1">Date & Time</p>
                                                <p><span class="text-grey-500">From:</span> {{ $booking->date->formatted_date }} - {{ $booking->timeSlot->formatted_time }}</p>
                                                <p><span class="text-grey-500">To:</span> <span x-text="selectedDateText"></span> - <span x-text="selectedTimeSlotText"></span></p>
                                            </div>
                                            <div class="p-3 bg-grey-50 rounded-lg">
                                                <p class="font-medium text-grey-700 mb-1">Tables</p>
                                                <p><span class="text-grey-500">Current:</span> <span x-text="currentTablesText"></span></p>
                                                <p><span class="text-grey-500">New:</span> <span x-text="selectedTablesText"></span></p>
                                                <p class="mt-1"><span class="text-grey-500">Capacity:</span> <span x-text="selectedCapacity"></span> pax (need <span x-text="requiredPax"></span> pax)</p>
                                            </div>
                                        </div>
                                        <p class="mt-3 text-sm text-grey-500">
                                            This action cannot be undone. Continue?
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
                                class="mt-3 w-full inline-flex justify-center rounded-md shadow-sm px-4 py-2 bg-grey-600 text-base font-medium text-white hover:bg-grey-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-grey-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @php
        $datesJson = $dates->map(function ($d) {
            return [
                'id' => $d->id,
                'formatted_date' => $d->formatted_date,
                'date_value' => $d->date_value->format('Y-m-d'),
            ];
        });
        $timeSlotsJson = $timeSlots->map(function ($t) {
            return [
                'id' => $t->id,
                'formatted_time' => $t->formatted_time,
            ];
        });
        $allTablesJson = $allTables->map(function ($t) {
            return [
                'id' => $t->id,
                'table_number' => $t->table_number,
                'capacity' => $t->capacity,
                'seat_type' => $t->seat_type,
            ];
        });
        $availableTablesJson = $availableTables->map(function ($t) {
            return [
                'id' => $t->id,
                'table_number' => $t->table_number,
                'capacity' => $t->capacity,
                'seat_type' => $t->seat_type,
            ];
        });
    @endphp
    <script>
        function amendBookingForm() {
            return {
                dates: @json($datesJson),
                timeSlots: @json($timeSlotsJson),
                allTables: @json($allTablesJson),
                availableTables: @json($availableTablesJson),
                selectedDateId: {{ $booking->date_id }},
                selectedTimeSlotId: {{ $booking->time_slot_id }},
                selectedTableIds: @json($currentTableIds),
                currentTableIds: @json($currentTableIds),
                dateSearch: '{{ $booking->date->formatted_date }}',
                showDateDropdown: false,
                isChecking: false,
                availabilityResult: null,
                showConfirmModal: false,
                originalDateId: {{ $booking->date_id }},
                originalTimeSlotId: {{ $booking->time_slot_id }},
                requiredPax: {{ $booking->details->sum('quantity') }},

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

                get selectedCapacity() {
                    return this.selectedTableIds.reduce((sum, id) => {
                        const table = this.allTables.find(t => t.id === id);
                        return sum + (table ? table.capacity : 0);
                    }, 0);
                },

                get currentTablesText() {
                    return this.currentTableIds.map(id => {
                        const table = this.allTables.find(t => t.id === id);
                        return table ? `${table.table_number} (${table.capacity}-seater)` : '';
                    }).filter(t => t).join(', ') || 'None';
                },

                get selectedTablesText() {
                    return this.selectedTableIds.map(id => {
                        const table = this.allTables.find(t => t.id === id);
                        return table ? `${table.table_number} (${table.capacity}-seater)` : '';
                    }).filter(t => t).join(', ') || 'None';
                },

                get hasSlotChanged() {
                    return this.selectedDateId != this.originalDateId ||
                           this.selectedTimeSlotId != this.originalTimeSlotId;
                },

                get hasTablesChanged() {
                    if (this.selectedTableIds.length !== this.currentTableIds.length) return true;
                    const sortedSelected = [...this.selectedTableIds].sort((a, b) => a - b);
                    const sortedCurrent = [...this.currentTableIds].sort((a, b) => a - b);
                    return !sortedSelected.every((id, index) => id === sortedCurrent[index]);
                },

                get hasChanged() {
                    return this.hasSlotChanged || this.hasTablesChanged;
                },

                get canSubmit() {
                    return this.selectedDateId &&
                           this.selectedTimeSlotId &&
                           this.hasChanged &&
                           this.selectedTableIds.length > 0 &&
                           this.selectedCapacity >= this.requiredPax &&
                           !this.isChecking;
                },

                isTableAvailable(tableId) {
                    return this.availableTables.some(t => t.id === tableId);
                },

                isTableSelected(tableId) {
                    return this.selectedTableIds.includes(tableId);
                },

                isTableCurrent(tableId) {
                    return this.currentTableIds.includes(tableId);
                },

                getTableClasses(table) {
                    const isSelected = this.isTableSelected(table.id);
                    const isAvailable = this.isTableAvailable(table.id);
                    const isCurrent = this.isTableCurrent(table.id);

                    if (!isAvailable) {
                        return 'border-grey-300 bg-grey-100 opacity-60 cursor-not-allowed';
                    }

                    if (isSelected) {
                        return 'border-primary-500 bg-primary-50 cursor-pointer';
                    }

                    if (isCurrent && !this.hasSlotChanged) {
                        return 'border-secondary-500 bg-secondary-50 cursor-pointer';
                    }

                    return 'border-success-300 bg-success-50 hover:border-success-400 cursor-pointer';
                },

                getTableBadgeClasses(table) {
                    const isSelected = this.isTableSelected(table.id);
                    const isAvailable = this.isTableAvailable(table.id);
                    const isCurrent = this.isTableCurrent(table.id);

                    if (!isAvailable) {
                        return 'bg-grey-200 text-grey-600';
                    }

                    if (isSelected) {
                        return 'bg-primary-100 text-primary-700';
                    }

                    if (isCurrent && !this.hasSlotChanged) {
                        return 'bg-secondary-100 text-secondary-700';
                    }

                    return 'bg-success-100 text-success-700';
                },

                getTableBadgeText(table) {
                    const isSelected = this.isTableSelected(table.id);
                    const isAvailable = this.isTableAvailable(table.id);
                    const isCurrent = this.isTableCurrent(table.id);

                    if (!isAvailable) {
                        return 'Unavailable';
                    }

                    if (isSelected) {
                        return 'Selected';
                    }

                    if (isCurrent && !this.hasSlotChanged) {
                        return 'Current';
                    }

                    return 'Available';
                },

                toggleTable(table) {
                    if (!this.isTableAvailable(table.id)) return;

                    const index = this.selectedTableIds.indexOf(table.id);
                    if (index > -1) {
                        this.selectedTableIds.splice(index, 1);
                    } else {
                        this.selectedTableIds.push(table.id);
                    }
                },

                onDateFocus() {
                    this.dateSearch = '';
                    this.showDateDropdown = true;
                },

                onDateBlur() {
                    setTimeout(() => {
                        if (this.selectedDateId) {
                            const date = this.dates.find(d => d.id === this.selectedDateId);
                            if (date) {
                                this.dateSearch = date.formatted_date;
                            }
                        }
                    }, 200);
                },

                selectDate(date) {
                    this.selectedDateId = date.id;
                    this.dateSearch = date.formatted_date;
                    this.showDateDropdown = false;
                    this.onSlotChange();
                },

                async onSlotChange() {
                    if (!this.selectedDateId || !this.selectedTimeSlotId) return;
                    await this.checkAvailability();
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

                        const data = await response.json();

                        if (!response.ok) {
                            this.availabilityResult = {
                                available: false,
                                message: data.message || 'Validation failed. Please check your selection.',
                            };
                            return;
                        }

                        this.availabilityResult = data;

                        if (data.available_tables) {
                            this.availableTables = data.available_tables;
                        }

                        if (data.suggested_table_ids) {
                            this.selectedTableIds = [...data.suggested_table_ids];
                        }

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
                    const form = this.$refs.amendForm;

                    this.selectedTableIds.forEach(tableId => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'table_ids[]';
                        input.value = tableId;
                        form.appendChild(input);
                    });

                    form.submit();
                },

                init() {
                    this.availabilityResult = {
                        available: true,
                        message: 'Current slot - tables available.',
                    };
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
@endsection
