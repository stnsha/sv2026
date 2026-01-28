@extends('layouts.dashboard')

@section('title', 'Bookings for ' . $date->formatted_date)
@section('page-title')
    {{ $date->formatted_date }}
    <span class="block text-lg font-medium text-grey-500">Slot {{ $timeSlot->id }}: {{ $timeSlot->formatted_time }}</span>
@endsection
@section('page-description', 'View bookings for this time slot')

@push('styles')
    <style>[x-cloak] { display: none !important; }</style>
@endpush

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.capacity.index') }}" class="inline-block px-3 py-1.5 text-sm bg-grey-600 text-white rounded-lg hover:bg-grey-700">
            Back to Capacity
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 mb-6"
         data-bookings="{{ $bookings->map(fn($b) => ['name' => $b->customer->name, 'reference' => $b->reference_id])->values()->toJson() }}"
         x-data="{
            search: '',
            currentPage: 1,
            perPage: 10,
            bookings: [],
            get filteredIndices() {
                if (this.search.length === 0) {
                    return this.bookings.map((_, i) => i);
                }
                const term = this.search.toLowerCase();
                return this.bookings
                    .map((b, i) => ({ b, i }))
                    .filter(({ b }) => b.name.toLowerCase().includes(term) || b.reference.toLowerCase().includes(term))
                    .map(({ i }) => i);
            },
            get filteredCount() {
                return this.filteredIndices.length;
            },
            get totalPages() {
                return Math.max(1, Math.ceil(this.filteredCount / this.perPage));
            },
            get paginatedIndices() {
                const start = (this.currentPage - 1) * this.perPage;
                return this.filteredIndices.slice(start, start + this.perPage);
            },
            get showingFrom() {
                if (this.filteredCount === 0) return 0;
                return (this.currentPage - 1) * this.perPage + 1;
            },
            get showingTo() {
                return Math.min(this.currentPage * this.perPage, this.filteredCount);
            },
            isRowVisible(index) {
                return this.paginatedIndices.includes(index);
            },
            resetPage() {
                this.currentPage = 1;
            },
            prevPage() {
                if (this.currentPage > 1) this.currentPage--;
            },
            nextPage() {
                if (this.currentPage < this.totalPages) this.currentPage++;
            },
            goToPage(page) {
                this.currentPage = page;
            },
            get pageNumbers() {
                const pages = [];
                for (let i = 1; i <= this.totalPages; i++) {
                    pages.push(i);
                }
                return pages;
            }
         }"
         x-init="bookings = JSON.parse($el.dataset.bookings)"
    >
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-xl font-semibold text-grey-900">Bookings Overview</h2>
                <p class="text-sm text-grey-500 mt-1">
                    {{ $availabilitySummary['booked_tables'] }}/{{ $availabilitySummary['total_tables'] }} tables booked
                    <span class="mx-2">|</span>
                    6-seater: {{ $availabilitySummary['booked_six_seaters'] }}/{{ $availabilitySummary['total_six_seaters'] }}
                    <span class="mx-2">|</span>
                    4-seater: {{ $availabilitySummary['booked_four_seaters'] }}/{{ $availabilitySummary['total_four_seaters'] }}
                </p>
            </div>
            <div class="text-right">
                <p class="text-sm text-grey-500">Total Revenue</p>
                <p class="text-2xl font-bold text-success-600">RM {{ number_format($totalRevenue, 2) }}</p>
            </div>
        </div>

        @if($bookings->count() > 0)
            <div class="mb-4">
                <div class="relative">
                    <input
                        type="text"
                        x-model="search"
                        @input="resetPage()"
                        placeholder="Search by customer name or reference..."
                        class="w-full px-3 py-2 pl-9 text-sm border border-grey-200 rounded-lg bg-white text-grey-700 placeholder-grey-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    >
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-grey-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <p x-show="search.length > 0" x-cloak class="text-xs text-grey-500 mt-1">
                    Showing <span x-text="filteredCount"></span> of {{ $bookings->count() }} bookings
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-grey-200">
                    <thead class="bg-grey-50">
                        <tr>
                            <th class="text-left py-3 px-4 text-xs font-medium text-grey-500 uppercase tracking-wider">ID</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-grey-500 uppercase tracking-wider">Customer</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-grey-500 uppercase tracking-wider">Contact</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-grey-500 uppercase tracking-wider">Pax</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-grey-500 uppercase tracking-wider">Tables</th>
                            <th class="text-right py-3 px-4 text-xs font-medium text-grey-500 uppercase tracking-wider">Total</th>
                            <th class="text-center py-3 px-4 text-xs font-medium text-grey-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-grey-200">
                        @foreach($bookings as $booking)
                            <tr class="hover:bg-grey-50" x-show="isRowVisible({{ $loop->index }})">
                                <td class="py-3 px-4 text-sm text-grey-900">#{{ $booking->reference_id }}</td>
                                <td class="py-3 px-4 text-sm font-medium text-grey-900">{{ $booking->customer->name }}</td>
                                <td class="py-3 px-4">
                                    <div class="text-sm text-grey-900">{{ $booking->customer->email }}</div>
                                    <div class="text-xs text-grey-500">{{ $booking->customer->phone_number }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    @foreach($booking->details as $detail)
                                        @if($detail->quantity > 0)
                                            <div class="text-xs text-grey-600">
                                                {{ $detail->price->category }}: {{ $detail->quantity }}
                                            </div>
                                        @endif
                                    @endforeach
                                </td>
                                <td class="py-3 px-4">
                                    @foreach($booking->tableBookings as $tb)
                                        <span class="inline-block bg-primary-100 text-primary-700 text-xs px-2 py-1 rounded mr-1">
                                            {{ $tb->table->table_number }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="py-3 px-4 text-right text-sm font-medium text-grey-900">
                                    RM {{ number_format($booking->total, 2) }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <a href="{{ route('admin.bookings.show', ['booking' => $booking, 'from' => request()->query('from')]) }}"
                                       class="px-3 py-1.5 text-sm bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        <template x-if="filteredCount === 0 && search.length > 0">
                            <tr>
                                <td colspan="7" class="py-8 text-center text-grey-500">No bookings match your search.</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div x-show="totalPages > 1" x-cloak class="flex flex-col sm:flex-row items-center justify-between border-t border-grey-200 pt-4 mt-4 gap-3">
                <p class="text-sm text-grey-500">
                    Showing <span x-text="showingFrom"></span> to <span x-text="showingTo"></span> of <span x-text="filteredCount"></span> results
                </p>
                <div class="flex items-center gap-1">
                    <button
                        @click="prevPage()"
                        :disabled="currentPage === 1"
                        :class="currentPage === 1 ? 'text-grey-300 cursor-not-allowed' : 'text-grey-700 hover:bg-grey-100'"
                        class="px-3 py-1.5 text-sm border border-grey-200 rounded-lg"
                    >
                        Previous
                    </button>
                    <template x-for="page in pageNumbers" :key="page">
                        <button
                            @click="goToPage(page)"
                            :class="page === currentPage ? 'bg-primary-600 text-white border-primary-600' : 'text-grey-700 hover:bg-grey-100 border-grey-200'"
                            class="px-3 py-1.5 text-sm border rounded-lg"
                            x-text="page"
                        ></button>
                    </template>
                    <button
                        @click="nextPage()"
                        :disabled="currentPage === totalPages"
                        :class="currentPage === totalPages ? 'text-grey-300 cursor-not-allowed' : 'text-grey-700 hover:bg-grey-100'"
                        class="px-3 py-1.5 text-sm border border-grey-200 rounded-lg"
                    >
                        Next
                    </button>
                </div>
            </div>
        @else
            <p class="text-grey-500 text-center py-8">No bookings for this time slot</p>
        @endif
    </div>
@endsection
