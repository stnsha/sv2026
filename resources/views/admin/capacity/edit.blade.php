@extends('layouts.dashboard')

@section('title', 'Edit Capacity - ' . $date->formatted_date)
@section('page-title', $date->formatted_date . ' - ' . $timeSlot->formatted_time)
@section('page-description', 'Manage blocked tables for this time slot')

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
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                Save Changes
            </button>
        </div>
    </div>


    <form action="{{ route('admin.capacity.update', ['date' => $date, 'timeSlot' => $timeSlot]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-grey-900">Blocked Tables</h2>
                    <p class="text-sm text-grey-500 mt-1">
                        Select tables to block. Blocked tables will not be available for booking.
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
                    @endphp

                    @if ($isBooked)
                        {{-- Booked tables - disabled, cannot be blocked --}}
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
                        <label
                            class="table-label relative flex flex-col items-center p-4 border-2 rounded-lg cursor-pointer transition-all
                            {{ $isBlocked ? 'border-danger-500 bg-danger-50' : 'border-grey-200 hover:border-grey-300' }}">
                            <input type="checkbox" name="blocked_tables[]" value="{{ $table->id }}"
                                class="table-checkbox sr-only" {{ $isBlocked ? 'checked' : '' }}>
                            <div class="text-lg font-bold text-grey-900">{{ $table->table_number }}</div>
                            <div class="text-sm text-grey-500">{{ $table->capacity }}-seater</div>
                            <div class="mt-2">
                                <span
                                    class="status-badge px-2 py-1 text-xs rounded-full
                                    {{ $isBlocked ? 'bg-danger-100 text-danger-700' : 'bg-success-100 text-success-700' }}">
                                    {{ $isBlocked ? 'Blocked' : 'Available' }}
                                </span>
                            </div>
                        </label>
                    @endif
                @endforeach
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.table-checkbox');

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const label = this.closest('label');
                    const badge = label.querySelector('.status-badge');

                    if (this.checked) {
                        label.classList.remove('border-grey-200', 'hover:border-grey-300');
                        label.classList.add('border-danger-500', 'bg-danger-50');
                        badge.classList.remove('bg-success-100', 'text-success-700');
                        badge.classList.add('bg-danger-100', 'text-danger-700');
                        badge.textContent = 'Blocked';
                    } else {
                        label.classList.remove('border-danger-500', 'bg-danger-50');
                        label.classList.add('border-grey-200', 'hover:border-grey-300');
                        badge.classList.remove('bg-danger-100', 'text-danger-700');
                        badge.classList.add('bg-success-100', 'text-success-700');
                        badge.textContent = 'Available';
                    }
                });
            });

            document.getElementById('block-all').addEventListener('click', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = true;
                    checkbox.dispatchEvent(new Event('change'));
                });
            });

            document.getElementById('unblock-all').addEventListener('click', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                    checkbox.dispatchEvent(new Event('change'));
                });
            });
        });
    </script>
@endsection
