@extends('layouts.dashboard')

@section('title', 'Edit Capacity - ' . $date->formatted_date)
@section('page-title', $date->formatted_date)
@section('page-description', 'Manage blocked tables for this date')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.capacity.index') }}" class="text-primary-600 hover:text-primary-700 text-sm">
            Back to Capacity
        </a>
    </div>

    <form action="{{ route('admin.capacity.update', $date) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-grey-900">Blocked Tables</h2>
                    <p class="text-sm text-grey-500 mt-1">
                        Select tables to block for {{ $date->date_value->format('l, d M Y') }}.
                        Blocked tables will not be available for booking.
                    </p>
                </div>
                <div class="flex gap-2">
                    <button type="button" id="block-all" class="px-3 py-1.5 text-sm border border-danger-600 text-danger-600 rounded-lg hover:bg-danger-50">
                        Block All
                    </button>
                    <button type="button" id="unblock-all" class="px-3 py-1.5 text-sm border border-success-600 text-success-600 rounded-lg hover:bg-success-50">
                        Unblock All
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($tables as $table)
                    <label class="relative flex flex-col items-center p-4 border-2 rounded-lg cursor-pointer transition-all
                        {{ in_array($table->id, $blockedTableIds) ? 'border-danger-500 bg-danger-50' : 'border-grey-200 hover:border-grey-300' }}">
                        <input
                            type="checkbox"
                            name="blocked_tables[]"
                            value="{{ $table->id }}"
                            class="table-checkbox sr-only"
                            {{ in_array($table->id, $blockedTableIds) ? 'checked' : '' }}
                        >
                        <div class="text-lg font-bold text-grey-900">{{ $table->table_number }}</div>
                        <div class="text-sm text-grey-500">{{ $table->capacity }}-seater</div>
                        <div class="mt-2">
                            <span class="status-badge px-2 py-1 text-xs rounded-full
                                {{ in_array($table->id, $blockedTableIds) ? 'bg-danger-100 text-danger-700' : 'bg-success-100 text-success-700' }}">
                                {{ in_array($table->id, $blockedTableIds) ? 'Blocked' : 'Available' }}
                            </span>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.capacity.index') }}"
               class="px-4 py-2 border border-grey-300 text-grey-700 rounded-lg hover:bg-grey-50">
                Cancel
            </a>
            <button type="submit"
                    class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                Save Changes
            </button>
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
