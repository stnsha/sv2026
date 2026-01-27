@extends('layouts.dashboard')

@section('title', 'Edit Capacity - ' . $date->formatted_date)
@section('page-title')
    {{ $date->formatted_date }}
    <span class="block text-lg font-medium text-grey-500">Slot {{ $timeSlot->id }}: {{ $timeSlot->formatted_time }}</span>
@endsection
@section('page-description', 'Manage blocked tables and capacity overrides for this time slot')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('admin.capacity.index') }}"
            class="inline-block px-3 py-1.5 text-sm bg-grey-600 text-white rounded-lg hover:bg-grey-700">
            Back to Capacity
        </a>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.capacity.index') }}"
                class="px-4 py-2 bg-grey-600 text-white rounded-lg hover:bg-grey-700">
                Cancel
            </a>
            <button type="submit" form="capacity-form" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                Save Changes
            </button>
        </div>
    </div>


    <form id="capacity-form" action="{{ route('admin.capacity.update', ['date' => $date, 'timeSlot' => $timeSlot]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-grey-900">Table Management</h2>
                    <p class="text-sm text-grey-500 mt-1">
                        Block tables or reduce their effective capacity for this time slot.
                    </p>
                </div>
                <div class="flex gap-2">
                    <button type="button" id="block-all"
                        class="px-3 py-1.5 text-sm bg-danger-600 text-white rounded-lg hover:bg-danger-700">
                        Block All
                    </button>
                    <button type="button" id="unblock-all"
                        class="px-3 py-1.5 text-sm bg-success-600 text-white rounded-lg hover:bg-success-700">
                        Unblock All
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach ($tables as $table)
                    @php
                        $isBooked = in_array($table->id, $bookedTableIds);
                        $isBlocked = in_array($table->id, $blockedTableIds);
                        $currentCapacity = $capacityOverrides[$table->id] ?? $table->capacity;
                        $isReduced = $currentCapacity < $table->capacity;
                        $capacityOptions = range(2, $table->capacity, 2);
                    @endphp

                    @if ($isBooked)
                        {{-- Booked tables - disabled, cannot be modified --}}
                        <div
                            class="relative flex flex-col items-center p-4 border-2 rounded-lg border-primary-500 bg-primary-50 opacity-75 cursor-not-allowed">
                            <div class="text-lg font-bold text-grey-900">{{ $table->table_number }}</div>
                            <div class="text-sm text-grey-500">{{ $table->capacity }}-seater</div>
                            <div class="mt-2">
                                <span class="px-2 py-1 text-xs rounded-full bg-primary-100 text-primary-700">
                                    Booked
                                </span>
                            </div>
                        </div>
                    @else
                        {{-- Available or Blocked tables - can be toggled --}}
                        <div
                            class="table-card relative flex flex-col items-center p-4 border-2 rounded-lg transition-all
                            {{ $isBlocked ? 'border-danger-500 bg-danger-50' : ($isReduced ? 'border-warning-500 bg-warning-50' : 'border-grey-200') }}"
                            data-table-id="{{ $table->id }}"
                            data-base-capacity="{{ $table->capacity }}">

                            <label class="absolute inset-0 cursor-pointer">
                                <input type="checkbox" name="blocked_tables[]" value="{{ $table->id }}"
                                    class="table-checkbox sr-only" {{ $isBlocked ? 'checked' : '' }}>
                            </label>

                            <div class="text-lg font-bold text-grey-900">{{ $table->table_number }}</div>
                            <div class="text-sm text-grey-500 base-capacity">{{ $table->capacity }}-seater</div>

                            <div class="mt-2 relative z-10">
                                <select name="capacity_overrides[{{ $table->id }}]"
                                    class="capacity-select text-xs px-2 py-1 rounded border
                                    {{ $isReduced ? 'border-warning-500 bg-warning-100 text-warning-800' : 'border-grey-300 bg-white text-grey-700' }}
                                    {{ $isBlocked ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    {{ $isBlocked ? 'disabled' : '' }}>
                                    @foreach ($capacityOptions as $option)
                                        <option value="{{ $option }}" {{ $currentCapacity == $option ? 'selected' : '' }}>
                                            {{ $option }} pax
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mt-2">
                                <span class="status-badge px-2 py-1 text-xs rounded-full
                                    {{ $isBlocked ? 'bg-danger-100 text-danger-700' : ($isReduced ? 'bg-warning-100 text-warning-700' : 'bg-success-100 text-success-700') }}">
                                    {{ $isBlocked ? 'Blocked' : ($isReduced ? 'Reduced' : 'Available') }}
                                </span>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tableCards = document.querySelectorAll('.table-card');
            const checkboxes = document.querySelectorAll('.table-checkbox');

            function updateCardState(card) {
                const checkbox = card.querySelector('.table-checkbox');
                const select = card.querySelector('.capacity-select');
                const badge = card.querySelector('.status-badge');
                const baseCapacity = parseInt(card.dataset.baseCapacity);
                const isBlocked = checkbox.checked;
                const selectedCapacity = parseInt(select.value);
                const isReduced = selectedCapacity < baseCapacity;

                card.classList.remove('border-danger-500', 'bg-danger-50', 'border-warning-500', 'bg-warning-50', 'border-grey-200');
                select.classList.remove('border-warning-500', 'bg-warning-100', 'text-warning-800', 'border-grey-300', 'bg-white', 'text-grey-700', 'opacity-50', 'cursor-not-allowed');
                badge.classList.remove('bg-danger-100', 'text-danger-700', 'bg-warning-100', 'text-warning-700', 'bg-success-100', 'text-success-700');

                if (isBlocked) {
                    card.classList.add('border-danger-500', 'bg-danger-50');
                    select.classList.add('border-grey-300', 'bg-white', 'text-grey-700', 'opacity-50', 'cursor-not-allowed');
                    select.disabled = true;
                    badge.classList.add('bg-danger-100', 'text-danger-700');
                    badge.textContent = 'Blocked';
                } else if (isReduced) {
                    card.classList.add('border-warning-500', 'bg-warning-50');
                    select.classList.add('border-warning-500', 'bg-warning-100', 'text-warning-800');
                    select.disabled = false;
                    badge.classList.add('bg-warning-100', 'text-warning-700');
                    badge.textContent = 'Reduced';
                } else {
                    card.classList.add('border-grey-200');
                    select.classList.add('border-grey-300', 'bg-white', 'text-grey-700');
                    select.disabled = false;
                    badge.classList.add('bg-success-100', 'text-success-700');
                    badge.textContent = 'Available';
                }
            }

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const card = this.closest('.table-card');
                    updateCardState(card);
                });
            });

            document.querySelectorAll('.capacity-select').forEach(select => {
                select.addEventListener('change', function() {
                    const card = this.closest('.table-card');
                    updateCardState(card);
                });

                select.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            });

            document.getElementById('block-all').addEventListener('click', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = true;
                    const card = checkbox.closest('.table-card');
                    updateCardState(card);
                });
            });

            document.getElementById('unblock-all').addEventListener('click', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                    const card = checkbox.closest('.table-card');
                    updateCardState(card);
                });
            });
        });
    </script>
@endsection
