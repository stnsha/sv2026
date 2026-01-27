<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Port steamboat dan BBQ grill terbaik di Seremban yang menawarkan konsep makan sepuas hati dengan pilihan daging tanpa had, dim sum, pasta, dan pencuci mulut.">
    <title>Sandvillage - Tempahan Meja</title>
    @vite(['resources/css/app.css'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen" style="background-color: #faf5e8; font-family: 'Inter', sans-serif;">
    <main x-data="bookingApp()" class="max-w-5xl mx-auto px-4 py-6 sm:py-10">
        {{-- Information Section with Hanging Logo --}}
        <section class="mb-8 relative" aria-label="Maklumat lokasi dan waktu operasi">
            {{-- Information Card --}}
            <div class="rounded-2xl px-5 py-5 sm:px-6 sm:py-6 shadow-lg" style="background-color: #F5E6C6;">
                {{-- Logo & SEO Text --}}
                <div class="flex flex-col md:flex-row items-center gap-4 md:gap-6 mb-6">
                    <img src="{{ asset('img/logo.PNG') }}" alt="Sandvillage" class="h-20 sm:h-24 flex-shrink-0">
                    <p class="text-[14px] md:text-[16px] text-[#5B3924] leading-relaxed text-justify">
                        Port <span class="font-semibold">steamboat</span> dan <span class="font-semibold">BBQ grill</span> terbaik di Seremban yang menawarkan konsep makan sepuas hati (unlimited buffet) dengan pilihan daging tanpa had, dim sum, pasta, dan pencuci mulut. Destinasi ini sangat sesuai untuk mereka yang mencari pengalaman makan lengkap, berbaloi, dan sedap.
                    </p>
                </div>

                {{-- Info Pills --}}
                <div class="space-y-3 mb-6">
                    {{-- Address Pill --}}
                    <div class="flex items-center gap-4 px-5 py-4 md:px-6 md:py-5 rounded-full border-2" style="background-color: #FFFFFF; border-color: #5B3924;">
                        <img src="{{ asset('img/map_9389497.png') }}" alt="" class="w-12 h-12 md:w-14 md:h-14 flex-shrink-0" aria-hidden="true">
                        <div>
                            <p class="text-[14px] md:text-[16px] font-medium text-[#5B3924] tracking-[0.05em]">Sandvillage BBQ Grill & Steamboat</p>
                            <p class="text-[12px] md:text-[14px] text-[#5B3924]">Lot 1429, Jalan Sikamat, Kampung Ujong Pasir, 70400 Seremban, Negeri Sembilan</p>
                        </div>
                    </div>
                    {{-- Opening Hours Pill --}}
                    <div class="flex items-center gap-4 px-5 py-4 md:px-6 md:py-5 rounded-full border-2" style="background-color: #FFFFFF; border-color: #5B3924;">
                        <img src="{{ asset('img/opening-hours_8118904.png') }}" alt="" class="w-12 h-12 md:w-14 md:h-14 flex-shrink-0" aria-hidden="true">
                        <div>
                            <p class="text-[14px] md:text-[16px] font-medium text-[#5B3924] tracking-[0.05em]">Isnin - Ahad</p>
                            <p class="text-[12px] md:text-[14px] text-[#5B3924]">Daripada 7:00 MLM sehingga 11.00 MLM</p>
                            <p class="text-[11px] md:text-[13px] text-[#5B3924] italic">*Untuk bulan puasa sahaja</p>
                        </div>
                    </div>
                </div>

                {{-- Pricing Cards --}}
                <div class="mb-6">
                    <h2 class="sr-only">Harga Tiket</h2>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach($prices as $price)
                        <article class="rounded-xl p-4 md:p-5 text-center border-2" style="background-color: #FFFFFF; border-color: #5B3924;">
                            <div class="flex justify-center mb-2">
                                @if(str_contains(strtolower($price->category), 'dewasa'))
                                    <img src="{{ asset('img/couple_3460225.png') }}" alt="" class="w-10 h-10 md:w-12 md:h-12" aria-hidden="true">
                                @elseif(str_contains(strtolower($price->category), 'warga') || str_contains(strtolower($price->category), 'emas'))
                                    <img src="{{ asset('img/family_3010442.png') }}" alt="" class="w-10 h-10 md:w-12 md:h-12" aria-hidden="true">
                                @else
                                    <img src="{{ asset('img/kids_3636254.png') }}" alt="" class="w-10 h-10 md:w-12 md:h-12" aria-hidden="true">
                                @endif
                            </div>
                            <p class="text-[12px] md:text-[14px] font-medium text-[#5B3924] tracking-[0.05em]">{{ $price->category }}</p>
                            <div class="flex items-start justify-center gap-0.5 mt-1">
                                <span class="text-[10px] md:text-[12px] text-[#5B3924]">RM</span>
                                <span class="text-2xl md:text-4xl font-bold text-[#5B3924]">{{ number_format($price->amount, 2) }}</span>
                            </div>
                        </article>
                        @endforeach
                    </div>
                </div>

                {{-- Time Slot Info Cards --}}
                <div class="flex flex-col sm:flex-row gap-3">
                    @foreach($timeSlots as $slot)
                    <div class="flex items-center gap-3 px-4 py-3 md:px-5 md:py-4 rounded-full border-2 flex-1" style="background-color: #FFFFFF; border-color: #5B3924;">
                        <img src="{{ asset('img/extra-time_8727099.png') }}" alt="" class="w-8 h-8 md:w-10 md:h-10 flex-shrink-0" aria-hidden="true">
                        <div>
                            <p class="text-[14px] md:text-[16px] font-medium text-[#5B3924] tracking-[0.05em]">{{ $loop->first ? 'Slot Pertama' : 'Slot Kedua' }}</p>
                            <p class="text-[12px] md:text-[14px] text-[#5B3924]">{{ $slot->formatted_time }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Section Title --}}
        <h2 class="text-2xl md:text-3xl font-bold text-[#5B3924] text-center mb-6">Tempah Sekarang</h2>

        {{-- Booking Form --}}
        <section aria-labelledby="booking-heading">
            <h3 id="booking-heading" class="sr-only">Borang Tempahan</h3>

            <form action="{{ route('booking.store') }}" method="POST" @submit="handleSubmit" class="rounded-2xl p-5 sm:p-6 shadow-lg overflow-hidden" style="background-color: #F5E6C6;">
                @csrf

                {{-- Date Picker --}}
                <fieldset class="mb-8">
                    <legend class="text-[14px] font-medium text-[#5B3924] tracking-[0.05em] mb-4">Pilih Tarikh</legend>
                    <div class="flex items-center gap-3">
                        {{-- Left Arrow --}}
                        <button type="button" @click="prevSlide()" :disabled="currentSlide === 0" class="w-10 h-10 flex-shrink-0 rounded-full flex items-center justify-center shadow-md transition-all disabled:opacity-30 disabled:cursor-not-allowed hover:shadow-lg border-2" style="background-color: #FFFFFF; border-color: #5B3924;" aria-label="Tarikh sebelumnya">
                            <svg class="w-5 h-5" style="color: #5B3924;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        </button>

                        {{-- Date Cards Grid --}}
                        <div class="flex-1 grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-5 gap-2">
                            @foreach($dates as $index => $date)
                            <label x-show="isDateVisible({{ $index }})" x-cloak
                                   :class="isDateSoldOut({{ $date->id }}) ? 'cursor-not-allowed' : 'cursor-pointer'">
                                <input type="radio" name="date_id" value="{{ $date->id }}"
                                       :checked="selectedDate == '{{ $date->id }}'"
                                       @change="onDateChange({{ $date->id }})"
                                       :disabled="isDateSoldOut({{ $date->id }})"
                                       class="sr-only peer">
                                <div class="py-3 px-2 rounded-xl flex flex-col items-center justify-center transition-all shadow-md bg-white hover:shadow-lg relative"
                                     :class="{
                                         'peer-checked:shadow-lg peer-checked:bg-[#F1D9A9]': !isDateSoldOut({{ $date->id }}),
                                         'opacity-50 grayscale': isDateSoldOut({{ $date->id }})
                                     }">
                                    <span class="text-[10px] font-medium uppercase tracking-wide text-[#5B3924]">{{ $date->date_value->locale('ms')->isoFormat('ddd') }}</span>
                                    <span class="text-xl font-bold text-[#5B3924]">{{ $date->date_value->format('d') }}</span>
                                    <span class="text-[10px] text-[#5B3924]">{{ $date->date_value->locale('ms')->isoFormat('MMM') }}</span>
                                    <span x-show="isDateSoldOut({{ $date->id }})"
                                          class="absolute -top-1 -right-1 bg-red-500 text-white text-[8px] font-bold px-1.5 py-0.5 rounded-full">
                                        Habis
                                    </span>
                                </div>
                            </label>
                            @endforeach
                        </div>

                        {{-- Right Arrow --}}
                        <button type="button" @click="nextSlide()" :disabled="currentSlide >= totalSlides - 1" class="w-10 h-10 flex-shrink-0 rounded-full flex items-center justify-center shadow-md transition-all disabled:opacity-30 disabled:cursor-not-allowed hover:shadow-lg border-2" style="background-color: #FFFFFF; border-color: #5B3924;" aria-label="Tarikh seterusnya">
                            <svg class="w-5 h-5" style="color: #5B3924;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </fieldset>

                {{-- Customer Details --}}
                <fieldset class="mb-6 space-y-4">
                    <div>
                        <label for="customer_name" class="block text-[14px] font-medium text-[#5B3924] tracking-[0.05em] mb-1">Nama Anda</label>
                        <input type="text" name="customer[name]" id="customer_name" x-model="customerName" @blur="validateField('name')"
                               class="w-full px-3 py-2.5 rounded-lg text-[14px] shadow-md outline-none focus:shadow-lg" style="background-color: #FFFFFF; color: #5B3924;"
                               :class="errors.name ? 'ring-2 ring-red-500' : ''"
                               placeholder="Nama penuh">
                        <p x-show="errors.name" x-text="errors.name" class="mt-1 text-[12px] text-red-600"></p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="customer_phone" class="block text-[14px] font-medium text-[#5B3924] tracking-[0.05em] mb-1">No. Telefon</label>
                            <div class="flex items-center w-full rounded-lg shadow-md focus-within:shadow-lg" style="background-color: #FFFFFF;"
                                 :class="errors.phone ? 'ring-2 ring-red-500' : ''">
                                <span class="pl-3 text-[14px] font-medium select-none pointer-events-none" style="color: #5B3924;">+6</span>
                                <input type="text" id="customer_phone_input" x-model="customerPhone" @blur="validateField('phone')" inputmode="numeric" maxlength="11"
                                       class="flex-1 px-1 py-2.5 rounded-r-lg text-[14px] outline-none bg-transparent" style="color: #5B3924;"
                                       placeholder="0123456789">
                                <input type="hidden" name="customer[phone_number]" :value="'+6' + customerPhone">
                            </div>
                            <p x-show="errors.phone" x-text="errors.phone" class="mt-1 text-[12px] text-red-600"></p>
                        </div>
                        <div>
                            <label for="customer_email" class="block text-[14px] font-medium text-[#5B3924] tracking-[0.05em] mb-1">Email</label>
                            <input type="email" name="customer[email]" id="customer_email" x-model="customerEmail" @blur="validateField('email')"
                                   class="w-full px-3 py-2.5 rounded-lg text-[14px] shadow-md outline-none focus:shadow-lg" style="background-color: #FFFFFF; color: #5B3924;"
                                   :class="errors.email ? 'ring-2 ring-red-500' : ''"
                                   placeholder="contoh@email.com">
                            <p x-show="errors.email" x-text="errors.email" class="mt-1 text-[12px] text-red-600"></p>
                        </div>
                    </div>
                </fieldset>

                {{-- Guest Quantities --}}
                <fieldset class="mb-6 space-y-3">
                    <legend class="text-[14px] font-medium text-[#5B3924] tracking-[0.05em] mb-3">Bilangan Tetamu</legend>

                    @foreach($prices as $index => $price)
                    <div class="flex items-center justify-between rounded-lg p-3 shadow-md" style="background-color: #FFFFFF;">
                        <span class="text-[14px] font-medium text-[#5B3924]">{{ $price->category }}</span>
                        <div class="flex items-center gap-3">
                            <input type="hidden" name="pax_details[{{ $index }}][price_id]" value="{{ $price->id }}">
                            <div class="flex items-center">
                                <button type="button" @click="decrementQty({{ $index }})" class="w-8 h-8 rounded-full flex items-center justify-center shadow-md hover:shadow-lg flex-shrink-0" style="background-color: #F5E6C6; color: #5B3924;" aria-label="Kurang">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                </button>
                                <span class="w-10 text-center text-[14px] font-semibold" style="color: #5B3924;" x-text="quantities[{{ $index }}]"></span>
                                <input type="hidden" name="pax_details[{{ $index }}][quantity]" x-model.number="quantities[{{ $index }}]">
                                <button type="button" @click="incrementQty({{ $index }})" class="w-8 h-8 rounded-full flex items-center justify-center shadow-md hover:shadow-lg flex-shrink-0" style="background-color: #5B3924; color: #faf5e8;" aria-label="Tambah">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </button>
                            </div>
                            <span class="text-[14px] font-semibold w-24 text-right" style="color: #5B3924;" x-text="'RM ' + (quantities[{{ $index }}] * {{ $price->amount }}).toFixed(2)"></span>
                        </div>
                    </div>
                    @endforeach
                    <p x-show="errors.pax" x-text="errors.pax" class="mt-2 text-[12px] text-red-600"></p>
                </fieldset>

                {{-- Time Slot Selection --}}
                <fieldset class="mb-6">
                    <legend class="text-[14px] font-medium text-[#5B3924] tracking-[0.05em] mb-3">Pilih Slot Masa</legend>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($timeSlots as $slot)
                        <label :class="isSlotAvailable({{ $slot->id }}) ? 'cursor-pointer' : 'cursor-not-allowed'">
                            <input type="radio" name="time_slot_id" value="{{ $slot->id }}"
                                   :checked="selectedTimeSlot == '{{ $slot->id }}'"
                                   @change="onTimeSlotChange({{ $slot->id }})"
                                   :disabled="!isSlotAvailable({{ $slot->id }})"
                                   class="sr-only peer">
                            <div class="py-3 px-4 rounded-lg shadow-md transition-all text-center bg-white hover:shadow-lg relative"
                                 :class="{
                                     'peer-checked:shadow-lg peer-checked:bg-[#F1D9A9]': isSlotAvailable({{ $slot->id }}),
                                     'opacity-50 grayscale': !isSlotAvailable({{ $slot->id }})
                                 }">
                                <span class="text-[14px] font-medium text-[#5B3924]">{{ $slot->formatted_time }}</span>
                                <span x-show="isSlotAvailable({{ $slot->id }})"
                                      class="block text-[12px] text-green-600 font-medium mt-1"
                                      x-text="getSlotAvailablePax({{ $slot->id }}) + ' pax tersedia'">
                                </span>
                                <span x-show="!isSlotAvailable({{ $slot->id }})"
                                      class="block text-[12px] text-red-500 font-bold mt-1">
                                    Habis
                                </span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </fieldset>

                {{-- Availability Status --}}
                <div x-show="availabilityMessage" x-cloak class="mb-4 p-3 rounded-lg text-[14px] border-2"
                     :class="isAvailable ? 'bg-green-100 text-green-700 border-green-300' : 'bg-red-100 text-red-700 border-red-300'"
                     x-text="availabilityMessage">
                </div>

                {{-- Summary & Total --}}
                <div class="mb-6 flex justify-end">
                    <div class="space-y-2 min-w-[300px]">
                        <div x-show="selectedDate && selectedTimeSlot" x-cloak
                             class="flex items-center justify-between gap-6 rounded-lg px-4 py-2.5 shadow-md" style="background-color: #FFFFFF;">
                            <span class="text-[14px] font-medium text-[#5B3924] tracking-[0.05em]">Tarikh</span>
                            <span class="text-[14px] font-semibold text-[#5B3924]" x-text="dateLabels[selectedDate] || ''"></span>
                        </div>
                        <div x-show="selectedDate && selectedTimeSlot" x-cloak
                             class="flex items-center justify-between gap-6 rounded-lg px-4 py-2.5 shadow-md" style="background-color: #FFFFFF;">
                            <span class="text-[14px] font-medium text-[#5B3924] tracking-[0.05em]">Slot Masa</span>
                            <span class="text-[14px] font-semibold text-[#5B3924]" x-text="timeSlotLabels[selectedTimeSlot] || ''"></span>
                        </div>
                        <div class="flex items-center justify-between gap-6 rounded-lg px-4 py-2.5 shadow-md" style="background-color: #FFFFFF;">
                            <span class="text-[14px] font-medium text-[#5B3924] tracking-[0.05em]">Jumlah Semua</span>
                            <span class="text-[14px] font-bold text-[#5B3924]" x-text="'RM ' + total.toFixed(2)"></span>
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="flex justify-end">
                    <button type="submit"
                            :disabled="!canSubmit"
                            class="py-3 px-8 font-semibold rounded-lg text-[14px] transition-all border-2 disabled:opacity-50 disabled:cursor-not-allowed" style="background-color: #5B3924; border-color: #5B3924; color: #faf5e8;">
                        Bayar Sekarang
                    </button>
                </div>
            </form>
        </section>
    </main>

    <script>
        function bookingApp() {
            const prices = @json($prices);
            const totalDates = {{ $dates->count() }};
            const dateIds = @json($dates->pluck('id'));
            const timeSlotIds = @json($timeSlots->pluck('id'));

            return {
                selectedDate: '',
                selectedTimeSlot: '',
                quantities: prices.map(() => 0),
                prices: prices.map(p => parseFloat(p.amount)),
                availabilityMessage: '',
                isAvailable: false,
                currentSlide: 0,
                windowWidth: window.innerWidth,
                customerName: '',
                customerPhone: '',
                customerEmail: '',
                errors: {
                    name: '',
                    phone: '',
                    email: '',
                    pax: ''
                },
                slotAvailability: @json($slotAvailability),
                soldOutDates: @json($soldOutDates),
                dateLabels: {
                    @foreach($dates as $date)
                        {{ $date->id }}: "{{ $date->date_value->locale('ms')->isoFormat('ddd, D MMM') }}",
                    @endforeach
                },
                timeSlotLabels: {
                    @foreach($timeSlots as $slot)
                        {{ $slot->id }}: "{{ $slot->formatted_time }}",
                    @endforeach
                },

                init() {
                    window.addEventListener('resize', () => {
                        this.windowWidth = window.innerWidth;
                        if (this.currentSlide >= this.totalSlides) {
                            this.currentSlide = Math.max(0, this.totalSlides - 1);
                        }
                    });

                    // Select first available date
                    for (const dateId of dateIds) {
                        if (!this.isDateSoldOut(dateId)) {
                            this.selectedDate = String(dateId);
                            break;
                        }
                    }

                    // Select first available time slot for the selected date
                    if (this.selectedDate) {
                        for (const slotId of timeSlotIds) {
                            if (this.isSlotAvailable(slotId)) {
                                this.selectedTimeSlot = String(slotId);
                                break;
                            }
                        }
                    }
                },

                isDateSoldOut(dateId) {
                    return this.soldOutDates.includes(Number(dateId));
                },

                isSlotAvailable(slotId) {
                    if (!this.selectedDate || !this.slotAvailability[this.selectedDate]) {
                        return false;
                    }
                    return this.slotAvailability[this.selectedDate][slotId] > 0;
                },

                getSlotAvailablePax(slotId) {
                    if (!this.selectedDate || !this.slotAvailability[this.selectedDate]) {
                        return 0;
                    }
                    return this.slotAvailability[this.selectedDate][slotId] || 0;
                },

                onDateChange(dateId) {
                    if (this.isDateSoldOut(dateId)) {
                        return;
                    }
                    this.selectedDate = String(dateId);

                    // Auto-select first available time slot for new date
                    this.selectedTimeSlot = '';
                    for (const slotId of timeSlotIds) {
                        if (this.isSlotAvailable(slotId)) {
                            this.selectedTimeSlot = String(slotId);
                            break;
                        }
                    }

                    this.checkAvailability();
                },

                onTimeSlotChange(slotId) {
                    if (!this.isSlotAvailable(slotId)) {
                        return;
                    }
                    this.selectedTimeSlot = String(slotId);
                    this.checkAvailability();
                },

                get datesPerSlide() {
                    // Default 5 for desktop, reduce for smaller screens
                    if (this.windowWidth < 640) return 3;  // mobile: 3
                    if (this.windowWidth < 1024) return 4; // tablet: 4
                    return 5; // desktop: 5 (default)
                },

                get totalSlides() {
                    return Math.ceil(totalDates / this.datesPerSlide);
                },

                isDateVisible(index) {
                    const start = this.currentSlide * this.datesPerSlide;
                    const end = start + this.datesPerSlide;
                    return index >= start && index < end;
                },

                prevSlide() {
                    if (this.currentSlide > 0) {
                        this.currentSlide--;
                    }
                },

                nextSlide() {
                    if (this.currentSlide < this.totalSlides - 1) {
                        this.currentSlide++;
                    }
                },

                get totalPax() {
                    return this.quantities.reduce((sum, qty) => sum + qty, 0);
                },

                get subtotal() {
                    return this.quantities.reduce((sum, qty, i) => sum + (qty * this.prices[i]), 0);
                },

                get total() {
                    return this.subtotal > 0 ? this.subtotal + 1 : 0;
                },

                get canSubmit() {
                    return this.totalPax > 0 && this.isAvailable && this.isFormValid();
                },

                isFormValid() {
                    return this.customerName.trim() !== '' &&
                           this.customerPhone.trim() !== '' &&
                           this.customerEmail.trim() !== '' &&
                           !this.errors.name &&
                           !this.errors.phone &&
                           !this.errors.email;
                },

                validateField(field) {
                    switch (field) {
                        case 'name':
                            if (!this.customerName.trim()) {
                                this.errors.name = 'Sila masukkan nama anda';
                            } else if (this.customerName.trim().length < 2) {
                                this.errors.name = 'Nama mestilah sekurang-kurangnya 2 aksara';
                            } else {
                                this.errors.name = '';
                            }
                            break;
                        case 'phone':
                            const phoneRegex = /^0\d{8,10}$/;
                            if (!this.customerPhone.trim()) {
                                this.errors.phone = 'Sila masukkan nombor telefon';
                            } else if (!phoneRegex.test(this.customerPhone.trim())) {
                                this.errors.phone = 'Format nombor telefon tidak sah (cth: 0123456789)';
                            } else {
                                this.errors.phone = '';
                            }
                            break;
                        case 'email':
                            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                            if (!this.customerEmail.trim()) {
                                this.errors.email = 'Sila masukkan alamat email';
                            } else if (!emailRegex.test(this.customerEmail.trim())) {
                                this.errors.email = 'Format email tidak sah';
                            } else {
                                this.errors.email = '';
                            }
                            break;
                    }
                },

                validateAllFields() {
                    this.validateField('name');
                    this.validateField('phone');
                    this.validateField('email');
                    if (this.totalPax < 1) {
                        this.errors.pax = 'Sila pilih sekurang-kurangnya 1 tetamu';
                    } else {
                        this.errors.pax = '';
                    }
                    return !this.errors.name && !this.errors.phone && !this.errors.email && !this.errors.pax;
                },

                incrementQty(index) {
                    this.quantities[index]++;
                    this.errors.pax = '';
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

                async checkAvailability() {
                    if (!this.selectedDate || !this.selectedTimeSlot || this.totalPax < 1) {
                        this.availabilityMessage = '';
                        this.isAvailable = false;
                        return;
                    }

                    // Check if slot is pre-computed as sold out
                    if (!this.isSlotAvailable(this.selectedTimeSlot)) {
                        this.isAvailable = false;
                        this.availabilityMessage = 'Maaf, slot ini telah habis dijual.';
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
                    if (!this.validateAllFields() || !this.isAvailable || this.totalPax < 1) {
                        e.preventDefault();
                        return;
                    }
                }
            };
        }

        // Malaysia Phone Input Handler
        (function() {
            const phoneInput = document.getElementById('customer_phone_input');
            const phoneHidden = document.getElementById('customer_phone');

            if (!phoneInput || !phoneHidden) return;

            function sanitize(value) {
                return value.replace(/[^0-9]/g, '');
            }

            function sync() {
                const clean = sanitize(phoneInput.value);
                phoneInput.value = clean;
                phoneHidden.value = clean ? '+6' + clean : '';
            }

            phoneInput.addEventListener('input', sync);

            phoneInput.addEventListener('paste', function(e) {
                e.preventDefault();
                let text = (e.clipboardData || window.clipboardData).getData('text');
                // Remove common prefixes: +6, +60, 6, 60
                text = text.replace(/^\+?6?0?/, '');
                let digits = sanitize(text);
                // Prepend 0 if missing
                if (digits.length > 0 && digits[0] !== '0') {
                    digits = '0' + digits;
                }
                phoneInput.value = digits.substring(0, 11);
                sync();
            });

            phoneInput.addEventListener('keydown', function(e) {
                // Allow: backspace, delete, tab, escape, enter, arrows
                if ([8, 9, 27, 13, 46, 37, 38, 39, 40].includes(e.keyCode)) return;
                // Allow Ctrl/Cmd + A, C, V, X
                if ((e.ctrlKey || e.metaKey) && [65, 67, 86, 88].includes(e.keyCode)) return;
                // Block non-numeric keys
                const isNumber = (e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105);
                if (!isNumber || e.shiftKey) {
                    e.preventDefault();
                }
            });
        })();
    </script>

    <style>
        [x-cloak] { display: none !important; }
        input::placeholder, select::placeholder {
            color: #5B3924;
            opacity: 0.6;
        }
    </style>
</body>
</html>
