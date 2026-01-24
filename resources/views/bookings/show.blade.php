<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pengesahan Tempahan #{{ $booking->reference_id }} - Sandvillage</title>
    @vite(['resources/css/app.css'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
</head>
<body class="min-h-screen" style="background-color: #faf5e8; font-family: 'Inter', sans-serif;">
    <main class="max-w-3xl mx-auto px-4 py-6 sm:py-10">
        {{-- Header with Logo --}}
        <div class="flex justify-center mb-6">
            <a href="{{ url('/') }}">
                <img src="{{ asset('img/logo.PNG') }}" alt="Sandvillage" class="h-16 sm:h-20">
            </a>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl border-2 border-green-400" style="background-color: #d4edda;">
                <p class="text-[14px] font-medium text-green-800 text-center">{{ session('success') }}</p>
            </div>
        @endif

        {{-- Invoice Card --}}
        <div class="rounded-2xl p-5 sm:p-8 shadow-lg" style="background-color: #F5E6C6;">
            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 pb-4 border-b-2" style="border-color: #5B3924;">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold" style="color: #5B3924;">Tempahan #{{ $booking->reference_id }}</h1>
                    <p class="text-[12px] sm:text-[14px] mt-1" style="color: #5B3924;">{{ now()->format('d M Y, g:i A') }}</p>
                </div>
                <div class="mt-3 sm:mt-0">
                    <span class="inline-block px-4 py-1.5 rounded-full text-[12px] sm:text-[14px] font-semibold
                        @if($booking->status === \App\Models\Booking::STATUS_CONFIRMED) bg-green-100 text-green-800
                        @elseif($booking->status === \App\Models\Booking::STATUS_PENDING_PAYMENT) bg-yellow-100 text-yellow-800
                        @elseif($booking->status === \App\Models\Booking::STATUS_PAYMENT_FAILED) bg-red-100 text-red-800
                        @elseif($booking->status === \App\Models\Booking::STATUS_CANCELLED) bg-gray-100 text-gray-800
                        @else bg-blue-100 text-blue-800
                        @endif">
                        {{ $booking->status_label }}
                    </span>
                </div>
            </div>

            {{-- Booking & Customer Details --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                {{-- Booking Details --}}
                <div class="rounded-xl p-4 border-2" style="background-color: #FFFFFF; border-color: #5B3924;">
                    <h2 class="text-[14px] font-semibold tracking-[0.05em] mb-3" style="color: #5B3924;">Maklumat Tempahan</h2>
                    <dl class="space-y-2 text-[13px] sm:text-[14px]" style="color: #5B3924;">
                        <div class="flex justify-between">
                            <dt>Tarikh:</dt>
                            <dd class="font-medium">{{ $booking->date->formatted_date }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt>Masa:</dt>
                            <dd class="font-medium">{{ $booking->timeSlot->formatted_time }}</dd>
                        </div>
                        <div class="flex justify-between items-start">
                            <dt>Meja:</dt>
                            <dd class="font-medium text-right">
                                @foreach($booking->tableBookings as $tb)
                                    <span class="inline-block px-2 py-0.5 rounded text-[12px] mb-1" style="background-color: #F5E6C6;">
                                        {{ $tb->table->table_number }}
                                    </span>
                                @endforeach
                            </dd>
                        </div>
                    </dl>
                </div>

                {{-- Customer Details --}}
                <div class="rounded-xl p-4 border-2" style="background-color: #FFFFFF; border-color: #5B3924;">
                    <h2 class="text-[14px] font-semibold tracking-[0.05em] mb-3" style="color: #5B3924;">Maklumat Pelanggan</h2>
                    <dl class="space-y-2 text-[13px] sm:text-[14px]" style="color: #5B3924;">
                        <div class="flex justify-between">
                            <dt>Nama:</dt>
                            <dd class="font-medium">{{ $booking->customer->name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt>Email:</dt>
                            <dd class="font-medium text-right break-all">{{ $booking->customer->email }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt>Telefon:</dt>
                            <dd class="font-medium">{{ $booking->customer->phone_number }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Guest Breakdown Table --}}
            <div class="rounded-xl p-4 border-2 mb-6" style="background-color: #FFFFFF; border-color: #5B3924;">
                <h2 class="text-[14px] font-semibold tracking-[0.05em] mb-3" style="color: #5B3924;">Butiran Tetamu</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-[13px] sm:text-[14px]" style="color: #5B3924;">
                        <thead>
                            <tr class="border-b-2" style="border-color: #5B3924;">
                                <th class="text-left py-2 font-semibold">Kategori</th>
                                <th class="text-center py-2 font-semibold">Bil.</th>
                                <th class="text-right py-2 font-semibold">Harga</th>
                                <th class="text-right py-2 font-semibold">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($booking->details as $detail)
                                <tr class="border-b" style="border-color: rgba(91, 57, 36, 0.2);">
                                    <td class="py-2">{{ $detail->price->category }}</td>
                                    <td class="text-center py-2">{{ $detail->quantity }}</td>
                                    <td class="text-right py-2">RM {{ number_format($detail->price->amount, 2) }}</td>
                                    <td class="text-right py-2">RM {{ number_format($detail->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-b" style="border-color: rgba(91, 57, 36, 0.2);">
                                <td colspan="3" class="py-2 text-right">Subtotal:</td>
                                <td class="py-2 text-right">RM {{ number_format($booking->subtotal, 2) }}</td>
                            </tr>
                            <tr class="border-b" style="border-color: rgba(91, 57, 36, 0.2);">
                                <td colspan="3" class="py-2 text-right">Caj Perkhidmatan:</td>
                                <td class="py-2 text-right">RM {{ number_format($booking->service_charge, 2) }}</td>
                            </tr>
                            @if($booking->discount > 0)
                                <tr class="border-b" style="border-color: rgba(91, 57, 36, 0.2);">
                                    <td colspan="3" class="py-2 text-right">Diskaun:</td>
                                    <td class="py-2 text-right">-RM {{ number_format($booking->discount, 2) }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td colspan="3" class="py-3 text-right font-bold text-[15px] sm:text-[16px]">Jumlah Keseluruhan:</td>
                                <td class="py-3 text-right font-bold text-[15px] sm:text-[16px]">RM {{ number_format($booking->total, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Payment Information --}}
            @if($booking->status === \App\Models\Booking::STATUS_CONFIRMED && ($booking->transaction_reference_no || $booking->transaction_time))
                <div class="rounded-xl p-4 border-2 mb-6" style="background-color: #FFFFFF; border-color: #5B3924;">
                    <h2 class="text-[14px] font-semibold tracking-[0.05em] mb-3" style="color: #5B3924;">Maklumat Pembayaran</h2>
                    <dl class="space-y-2 text-[13px] sm:text-[14px]" style="color: #5B3924;">
                        @if($booking->transaction_reference_no)
                            <div class="flex justify-between">
                                <dt>Rujukan Transaksi:</dt>
                                <dd class="font-medium">{{ $booking->transaction_reference_no }}</dd>
                            </div>
                        @endif
                        @if($booking->transaction_time)
                            <div class="flex justify-between">
                                <dt>Masa Transaksi:</dt>
                                <dd class="font-medium">{{ $booking->transaction_time->format('d M Y, g:i A') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            @endif

            {{-- Action Button --}}
            <div class="flex justify-center">
                <a href="{{ url('/') }}"
                   class="py-3 px-8 font-semibold rounded-lg text-[14px] transition-all border-2 hover:shadow-lg"
                   style="background-color: #5B3924; border-color: #5B3924; color: #faf5e8;">
                    Buat Tempahan Lain
                </a>
            </div>
        </div>

        {{-- Footer Note --}}
        <p class="text-center text-[12px] mt-6" style="color: #5B3924;">
            Sila simpan rujukan tempahan ini untuk rekod anda.
        </p>
    </main>
</body>
</html>
