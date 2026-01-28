PENGESAHAN TEMPAHAN #{{ $booking->reference_id }} - SAND VILLAGE
================================================================

Status: {{ $booking->status_label }}

MAKLUMAT TEMPAHAN
-----------------
Tarikh: {{ $booking->date->formatted_date }}
Masa:   {{ $booking->timeSlot->formatted_time }}
Meja:   {{ $booking->tableBookings->pluck('table.table_number')->implode(', ') }}

MAKLUMAT PELANGGAN
------------------
Nama:    {{ $booking->customer->name }}
Email:   {{ $booking->customer->email }}
Telefon: {{ $booking->customer->phone_number }}

BUTIRAN TETAMU
--------------
@foreach($booking->details as $detail)
{{ $detail->price->category }}  x{{ $detail->quantity }}  RM {{ number_format($detail->price->amount, 2) }}  = RM {{ number_format($detail->total, 2) }}
@endforeach

Subtotal: RM {{ number_format($booking->subtotal, 2) }}
@if($booking->discount > 0)
Diskaun:  -RM {{ number_format($booking->discount, 2) }}
@endif
Jumlah Keseluruhan: RM {{ number_format($booking->total, 2) }}
@if($booking->status === \App\Models\Booking::STATUS_CONFIRMED && ($booking->transaction_reference_no || $booking->transaction_time))

MAKLUMAT PEMBAYARAN
-------------------
@if($booking->transaction_reference_no)
Rujukan Transaksi: {{ $booking->transaction_reference_no }}
@endif
@if($booking->transaction_time)
Masa Transaksi:    {{ $booking->transaction_time->format('d M Y, g:i A') }}
@endif
@endif

Lihat tempahan anda di: {{ url('/booking/' . $booking->reference_id) }}

Sila simpan rujukan tempahan ini untuk rekod anda.
