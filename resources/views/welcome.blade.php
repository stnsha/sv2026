<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sandvillage - Tempah meja anda sekarang untuk pengalaman makan malam istimewa bersama keluarga dan rakan-rakan.">
    <title>Sandvillage - Tempahan Meja</title>
    @vite(['resources/css/app.css'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-secondary-100 min-h-screen">
    <main x-data="bookingApp()" class="max-w-3xl mx-auto px-4 py-6 sm:py-10">
        {{-- Header Section --}}
        <header class="text-center mb-8">
            <h1 class="text-4xl sm:text-5xl font-bold text-primary-600 mb-4" style="font-family: 'Instrument Sans', cursive;">
                Sandvillage
            </h1>
            <p class="text-grey-600 text-sm sm:text-base leading-relaxed max-w-lg mx-auto">
                Nikmati pengalaman makan malam istimewa di kawasan santai kami. Tempah meja anda sekarang untuk majlis keluarga, rakan-rakan atau pasangan tersayang.
            </p>
        </header>

        {{-- Info Cards --}}
        <section class="mb-8" aria-label="Maklumat lokasi dan waktu operasi">
            <div class="bg-secondary-200 rounded-2xl p-4 space-y-3">
                {{-- Location --}}
                <div class="flex items-center gap-3">
                    <img src="{{ asset('img/map_9389497.png') }}" alt="" class="w-8 h-8" aria-hidden="true">
                    <div>
                        <p class="text-xs text-grey-500 font-medium">Sandvillage BBQ @ 14 Strawberry</p>
                        <p class="text-xs text-grey-600">Lot 14004, Jalan Semarak, Kampung Ujung Pasir, Tanah Merah, Kelantan</p>
                    </div>
                </div>
                {{-- Operating Hours --}}
                <div class="flex items-center gap-3">
                    <img src="{{ asset('img/opening-hours_8118904.png') }}" alt="" class="w-8 h-8" aria-hidden="true">
                    <div>
                        <p class="text-xs text-grey-500 font-medium">Isnin - Ahad</p>
                        <p class="text-xs text-grey-600">Tempahan 7:00 PM sehingga 11:00 PM</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Pricing Cards --}}
        <section class="mb-8" aria-labelledby="pricing-heading">
            <h2 id="pricing-heading" class="sr-only">Harga Tiket</h2>
            <div class="grid grid-cols-3 gap-3">
                @foreach($prices as $price)
                <article class="bg-secondary-200 rounded-xl p-4 text-center border-2 border-secondary-300">
                    <div class="flex justify-center mb-2">
                        @if(str_contains(strtolower($price->category), 'dewasa'))
                            <img src="{{ asset('img/couple_3460225.png') }}" alt="" class="w-10 h-10" aria-hidden="true">
                        @elseif(str_contains(strtolower($price->category), 'warga') || str_contains(strtolower($price->category), 'emas'))
                            <img src="{{ asset('img/family_3010442.png') }}" alt="" class="w-10 h-10" aria-hidden="true">
                        @else
                            <img src="{{ asset('img/kids_3636254.png') }}" alt="" class="w-10 h-10" aria-hidden="true">
                        @endif
                    </div>
                    <p class="text-lg sm:text-xl font-bold text-primary-600">RM{{ number_format($price->amount, 2) }}</p>
                    <p class="text-xs text-grey-600 font-medium">{{ $price->category }}</p>
                </article>
                @endforeach
            </div>
        </section>

        {{-- Time Slot Info --}}
        <section class="mb-8">
            <div class="bg-secondary-200 rounded-xl p-3 flex items-center gap-3">
                <img src="{{ asset('img/extra-time_8727099.png') }}" alt="" class="w-8 h-8" aria-hidden="true">
                <div>
                    <p class="text-xs text-grey-500 font-medium">Slot Perjamuan</p>
                    <p class="text-xs text-grey-600">
                        @foreach($timeSlots as $slot)
                            {{ $slot->formatted_time }}@if(!$loop->last), @endif
                        @endforeach
                    </p>
                </div>
            </div>
        </section>

        {{-- Booking Form --}}
        <section aria-labelledby="booking-heading">
            <h2 id="booking-heading" class="text-xl font-bold text-primary-600 text-center mb-6">Tempah Sekarang</h2>

            <form action="{{ route('booking.store') }}" method="POST" @submit="handleSubmit" class="bg-grey-50 rounded-2xl p-5 sm:p-6 shadow-sm overflow-hidden">
                @csrf

                {{-- Date Picker Carousel - 5 dates per slide --}}
                <fieldset class="mb-6">
                    <legend class="text-sm font-semibold text-grey-700 mb-3">Pilih Tarikh</legend>
                    <div class="relative">
                        {{-- Carousel container --}}
                        <div class="overflow-hidden rounded-xl">
                            <div class="flex transition-transform duration-300 ease-out" :style="'transform: translateX(-' + (dateSlide * 100) + '%)'">
                                @php $chunks = $dates->chunk(5); @endphp
                                @foreach($chunks as $chunk)
                                <div class="grid grid-cols-5 gap-2 flex-shrink-0 w-full">
                                    @foreach($chunk as $date)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="date_id" value="{{ $date->id }}" x-model="selectedDate" @change="checkAvailability" class="sr-only peer" {{ $loop->parent->first && $loop->first ? 'checked' : '' }}>
                                        <div class="h-16 rounded-xl flex flex-col items-center justify-center transition-all peer-checked:bg-secondary-400 peer-checked:text-primary-700 bg-white text-grey-600 border border-grey-200 peer-checked:border-secondary-500 hover:border-secondary-400">
                                            <span class="text-[10px] font-medium uppercase tracking-wide">{{ $date->date_value->locale('ms')->isoFormat('ddd') }}</span>
                                            <span class="text-xl font-bold">{{ $date->date_value->format('d') }}</span>
                                        </div>
                                    </label>
                                    @endforeach
                                    {{-- Fill empty slots if chunk has less than 5 --}}
                                    @for($i = $chunk->count(); $i < 5; $i++)
                                    <div></div>
                                    @endfor
                                </div>
                                @endforeach
                            </div>
                        </div>
                        {{-- Navigation buttons - centered below --}}
                        @if($dates->count() > 5)
                        <div class="flex items-center justify-center gap-4 mt-4">
                            <button type="button" @click="scrollDates('left')" :disabled="dateSlide === 0" class="w-9 h-9 bg-white rounded-full shadow-sm flex items-center justify-center text-grey-500 hover:bg-secondary-100 hover:text-primary-600 transition-colors border border-grey-200 disabled:opacity-30 disabled:cursor-not-allowed" aria-label="Tarikh sebelumnya">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <span class="text-xs text-grey-500" x-text="(dateSlide + 1) + ' / ' + {{ $dates->chunk(5)->count() }}"></span>
                            <button type="button" @click="scrollDates('right')" :disabled="dateSlide >= {{ $dates->chunk(5)->count() - 1 }}" class="w-9 h-9 bg-white rounded-full shadow-sm flex items-center justify-center text-grey-500 hover:bg-secondary-100 hover:text-primary-600 transition-colors border border-grey-200 disabled:opacity-30 disabled:cursor-not-allowed" aria-label="Tarikh seterusnya">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                        @endif
                    </div>
                </fieldset>

                {{-- Customer Details --}}
                <fieldset class="mb-6 space-y-4">
                    <legend class="text-sm font-semibold text-grey-700 mb-3">Maklumat Anda</legend>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="customer_name" class="block text-xs font-medium text-grey-600 mb-1">Nama Anda</label>
                            <input type="text" name="customer[name]" id="customer_name" required
                                   class="w-full px-3 py-2.5 bg-white border border-grey-200 rounded-lg text-sm focus:ring-2 focus:ring-secondary-400 focus:border-secondary-400 outline-none"
                                   placeholder="Nama penuh">
                        </div>
                        <div>
                            <label for="customer_email" class="block text-xs font-medium text-grey-600 mb-1">Emel</label>
                            <input type="email" name="customer[email]" id="customer_email" required
                                   class="w-full px-3 py-2.5 bg-white border border-grey-200 rounded-lg text-sm focus:ring-2 focus:ring-secondary-400 focus:border-secondary-400 outline-none"
                                   placeholder="contoh@email.com">
                        </div>
                    </div>

                    <div>
                        <label for="customer_phone" class="block text-xs font-medium text-grey-600 mb-1">No. Telefon</label>
                        <input type="tel" name="customer[phone_number]" id="customer_phone" required
                               class="w-full px-3 py-2.5 bg-white border border-grey-200 rounded-lg text-sm focus:ring-2 focus:ring-secondary-400 focus:border-secondary-400 outline-none"
                               placeholder="012-3456789">
                    </div>
                </fieldset>

                {{-- Guest Quantities --}}
                <fieldset class="mb-6 space-y-3">
                    <legend class="text-sm font-semibold text-grey-700 mb-3">Bilangan Tetamu</legend>

                    @foreach($prices as $index => $price)
                    <div class="flex items-center justify-between bg-white border border-grey-200 rounded-lg p-3">
                        <div class="flex items-center gap-3">
                            @if(str_contains(strtolower($price->category), 'dewasa'))
                                <img src="{{ asset('img/couple_3460225.png') }}" alt="" class="w-6 h-6" aria-hidden="true">
                            @elseif(str_contains(strtolower($price->category), 'warga') || str_contains(strtolower($price->category), 'emas'))
                                <img src="{{ asset('img/family_3010442.png') }}" alt="" class="w-6 h-6" aria-hidden="true">
                            @else
                                <img src="{{ asset('img/kids_3636254.png') }}" alt="" class="w-6 h-6" aria-hidden="true">
                            @endif
                            <span class="text-sm font-medium text-grey-700">{{ $price->category }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <input type="hidden" name="pax_details[{{ $index }}][price_id]" value="{{ $price->id }}">
                            <div class="flex items-center gap-2">
                                <button type="button" @click="decrementQty({{ $index }})" class="w-8 h-8 rounded-full bg-grey-100 text-grey-600 flex items-center justify-center hover:bg-grey-200" aria-label="Kurang">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                </button>
                                <input type="number" name="pax_details[{{ $index }}][quantity]" x-model.number="quantities[{{ $index }}]" @input="updateTotals(); checkAvailability()" min="0" readonly
                                       class="w-10 text-center text-sm font-semibold text-grey-700 bg-transparent border-none outline-none">
                                <button type="button" @click="incrementQty({{ $index }})" class="w-8 h-8 rounded-full bg-secondary-300 text-primary-600 flex items-center justify-center hover:bg-secondary-400" aria-label="Tambah">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </button>
                            </div>
                            <span class="text-sm font-semibold text-primary-600 w-20 text-right" x-text="'RM' + (quantities[{{ $index }}] * {{ $price->amount }}).toFixed(2)"></span>
                        </div>
                    </div>
                    @endforeach
                </fieldset>

                {{-- Time Slot Selection --}}
                <fieldset class="mb-6">
                    <legend class="text-sm font-semibold text-grey-700 mb-3">Pilih Slot Masa</legend>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach($timeSlots as $slot)
                        <label class="cursor-pointer">
                            <input type="radio" name="time_slot_id" value="{{ $slot->id }}" x-model="selectedTimeSlot" @change="checkAvailability" class="sr-only peer" {{ $loop->first ? 'checked' : '' }}>
                            <div class="p-3 rounded-lg border transition-all peer-checked:bg-secondary-200 peer-checked:border-secondary-400 bg-white border-grey-200 text-center">
                                <span class="text-sm font-medium text-grey-700 peer-checked:text-primary-600">{{ $slot->formatted_time }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </fieldset>

                {{-- Availability Status --}}
                <div x-show="availabilityMessage" x-cloak class="mb-4 p-3 rounded-lg text-sm"
                     :class="isAvailable ? 'bg-success-100 text-success-700' : 'bg-danger-100 text-danger-700'"
                     x-text="availabilityMessage">
                </div>

                {{-- Total --}}
                <div class="bg-secondary-100 rounded-xl p-4 mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-grey-600">Jumlah Tetamu</span>
                        <span class="text-sm font-semibold text-grey-700" x-text="totalPax + ' orang'"></span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-grey-600">Subtotal</span>
                        <span class="text-sm text-grey-700" x-text="'RM' + subtotal.toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-grey-600">Caj Perkhidmatan</span>
                        <span class="text-sm text-grey-700">RM1.00</span>
                    </div>
                    <div class="border-t border-secondary-300 pt-2 mt-2">
                        <div class="flex justify-between items-center">
                            <span class="text-base font-bold text-grey-700">Jumlah Semua</span>
                            <span class="text-xl font-bold text-primary-600" x-text="'RM' + total.toFixed(2)"></span>
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <button type="submit"
                        :disabled="!canSubmit"
                        class="w-full py-3.5 px-4 bg-primary-600 text-white font-semibold rounded-xl transition-all hover:bg-primary-700 focus:ring-2 focus:ring-primary-400 focus:ring-offset-2 disabled:bg-grey-300 disabled:cursor-not-allowed">
                    Tempah Sekarang
                </button>
            </form>
        </section>

        {{-- Footer --}}
        <footer class="text-center mt-8 text-xs text-grey-500">
            <p>Sandvillage BBQ @ 14 Strawberry</p>
        </footer>
    </main>

    <script>
        function bookingApp() {
            const prices = @json($prices);

            const maxSlide = {{ $dates->chunk(5)->count() - 1 }};

            return {
                selectedDate: '{{ $dates->first()?->id }}',
                selectedTimeSlot: '{{ $timeSlots->first()?->id }}',
                quantities: prices.map(() => 0),
                prices: prices.map(p => parseFloat(p.amount)),
                availabilityMessage: '',
                isAvailable: false,
                dateSlide: 0,

                get totalPax() {
                    return this.quantities.reduce((sum, qty) => sum + qty, 0);
                },

                get subtotal() {
                    return this.quantities.reduce((sum, qty, i) => sum + (qty * this.prices[i]), 0);
                },

                get total() {
                    return this.subtotal + 1;
                },

                get canSubmit() {
                    return this.totalPax > 0 && this.isAvailable;
                },

                incrementQty(index) {
                    this.quantities[index]++;
                    this.checkAvailability();
                },

                decrementQty(index) {
                    if (this.quantities[index] > 0) {
                        this.quantities[index]--;
                        this.checkAvailability();
                    }
                },

                updateTotals() {
                    // Reactivity handled by Alpine
                },

                scrollDates(direction) {
                    if (direction === 'left' && this.dateSlide > 0) {
                        this.dateSlide--;
                    } else if (direction === 'right' && this.dateSlide < maxSlide) {
                        this.dateSlide++;
                    }
                },

                async checkAvailability() {
                    if (!this.selectedDate || !this.selectedTimeSlot || this.totalPax < 1) {
                        this.availabilityMessage = '';
                        this.isAvailable = false;
                        return;
                    }

                    try {
                        const response = await fetch('{{ route('booking.check-availability') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                date_id: this.selectedDate,
                                time_slot_id: this.selectedTimeSlot,
                                total_pax: this.totalPax
                            })
                        });

                        const data = await response.json();

                        if (data.available) {
                            this.isAvailable = true;
                            let tables = [];
                            if (data.tables_needed.six_seaters > 0) tables.push(data.tables_needed.six_seaters + ' x meja 6-tempat duduk');
                            if (data.tables_needed.four_seaters > 0) tables.push(data.tables_needed.four_seaters + ' x meja 4-tempat duduk');
                            this.availabilityMessage = 'Meja tersedia! ' + tables.join(', ');
                        } else {
                            this.isAvailable = false;
                            this.availabilityMessage = 'Maaf, meja tidak mencukupi untuk ' + this.totalPax + ' orang tetamu.';
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        this.availabilityMessage = '';
                        this.isAvailable = false;
                    }
                },

                handleSubmit(e) {
                    if (!this.canSubmit) {
                        e.preventDefault();
                    }
                }
            };
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</body>
</html>
