@extends('layouts.dashboard')

@section('title', 'Tables')
@section('page-title', 'Tables')
@section('page-description', 'Manage your restaurant table inventory')

@section('content')
    <!-- Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-sm text-grey-500">Total Tables</p>
            <p class="text-2xl font-bold text-grey-900">{{ $totalTables }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-sm text-grey-500">Total Capacity</p>
            <p class="text-2xl font-bold text-grey-900">{{ $totalCapacity }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-sm text-grey-500">Available Today</p>
            <p class="text-2xl font-bold text-success-600">{{ $availableCount }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-sm text-grey-500">Booked Today</p>
            <p class="text-2xl font-bold text-warning-600">{{ $bookedCount }}</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <x-table-filter
        :route="route('admin.tables.index')"
        :filters="[
            [
                'name' => 'seat_type',
                'placeholder' => 'All Seat Types',
                'options' => collect($seatTypes)->mapWithKeys(fn ($type) => [$type => ucfirst($type)])->toArray(),
            ],
            [
                'name' => 'today_status',
                'placeholder' => 'All Statuses',
                'options' => [
                    'available' => 'Available',
                    'booked' => 'Booked',
                ],
            ],
        ]"
        searchPlaceholder="Search table number..."
    />

    <!-- Tables List -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-4 lg:px-6 py-4 border-b border-grey-200">
            <h2 class="text-lg font-semibold text-grey-900">All Tables</h2>
            <p class="text-sm text-grey-500">Restaurant seating inventory</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-grey-200">
                <thead class="bg-grey-50">
                    <tr>
                        <th class="px-4 lg:px-6 py-3 text-left">
                            <x-sortable-header
                                column="table_number"
                                label="Table"
                                :currentSort="$currentSort"
                                :currentDirection="$currentDirection"
                            />
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-left">
                            <x-sortable-header
                                column="seat_type"
                                label="Seat Type"
                                :currentSort="$currentSort"
                                :currentDirection="$currentDirection"
                            />
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-left">
                            <x-sortable-header
                                column="capacity"
                                label="Capacity"
                                :currentSort="$currentSort"
                                :currentDirection="$currentDirection"
                            />
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider">
                            Today's Status
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-grey-200">
                    @forelse($tables as $table)
                        <tr class="hover:bg-grey-50">
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-grey-900">Table {{ $table->table_number }}</div>
                            </td>
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-secondary-100 text-primary-600">
                                    {{ ucfirst($table->seat_type) }}
                                </span>
                            </td>
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-grey-600">
                                {{ $table->capacity }} {{ $table->capacity === 1 ? 'seat' : 'seats' }}
                            </td>
                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                @if(in_array($table->id, $bookedTableIds))
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-warning-100 text-warning-700">
                                        Booked
                                    </span>
                                @else
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-success-100 text-success-700">
                                        Available
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 lg:px-6 py-8 text-center text-grey-500">
                                No tables found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tables->hasPages())
            <div class="px-4 lg:px-6 py-4 border-t border-grey-200">
                {{ $tables->links() }}
            </div>
        @endif
    </div>
@endsection
