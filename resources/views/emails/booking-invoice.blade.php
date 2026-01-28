<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pengesahan Tempahan #{{ $booking->reference_id }} - Sand Village</title>
</head>
<body style="margin: 0; padding: 0; background-color: #faf5e8; font-family: Arial, Helvetica, sans-serif; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;">
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background-color: #faf5e8;">
        <tr>
            <td align="center" style="padding: 24px 16px;">
                <!-- Main Container -->
                <table role="presentation" cellpadding="0" cellspacing="0" width="600" style="max-width: 600px; width: 100%; background-color: #F5E6C6; border-radius: 16px; overflow: hidden;">
                    <tr>
                        <td style="padding: 32px;">

                            <!-- Header -->
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-bottom: 2px solid #5B3924; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding-bottom: 16px;">
                                        <h1 style="margin: 0; font-size: 22px; font-weight: bold; color: #5B3924;">Tempahan #{{ $booking->reference_id }}</h1>
                                        <p style="margin: 4px 0 0 0; font-size: 13px; color: #5B3924;">{{ $booking->transaction_time ? $booking->transaction_time->format('d M Y, g:i A') : now()->format('d M Y, g:i A') }}</p>
                                    </td>
                                    <td align="right" valign="top" style="padding-bottom: 16px;">
                                        @if($booking->status === \App\Models\Booking::STATUS_CONFIRMED)
                                            <span style="display: inline-block; padding: 6px 16px; border-radius: 9999px; font-size: 13px; font-weight: 600; background-color: #dcfce7; color: #166534;">{{ $booking->status_label }}</span>
                                        @elseif($booking->status === \App\Models\Booking::STATUS_PENDING_PAYMENT)
                                            <span style="display: inline-block; padding: 6px 16px; border-radius: 9999px; font-size: 13px; font-weight: 600; background-color: #fef9c3; color: #854d0e;">{{ $booking->status_label }}</span>
                                        @elseif($booking->status === \App\Models\Booking::STATUS_PAYMENT_FAILED)
                                            <span style="display: inline-block; padding: 6px 16px; border-radius: 9999px; font-size: 13px; font-weight: 600; background-color: #fee2e2; color: #991b1b;">{{ $booking->status_label }}</span>
                                        @elseif($booking->status === \App\Models\Booking::STATUS_CANCELLED)
                                            <span style="display: inline-block; padding: 6px 16px; border-radius: 9999px; font-size: 13px; font-weight: 600; background-color: #f3f4f6; color: #1f2937;">{{ $booking->status_label }}</span>
                                        @else
                                            <span style="display: inline-block; padding: 6px 16px; border-radius: 9999px; font-size: 13px; font-weight: 600; background-color: #dbeafe; color: #1e40af;">{{ $booking->status_label }}</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            <!-- Booking & Customer Details -->
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 24px;">
                                <tr>
                                    <!-- Booking Details -->
                                    <td width="48%" valign="top" style="background-color: #FFFFFF; border: 2px solid #5B3924; border-radius: 12px; padding: 16px;">
                                        <h2 style="margin: 0 0 12px 0; font-size: 14px; font-weight: 600; letter-spacing: 0.05em; color: #5B3924;">Maklumat Tempahan</h2>
                                        <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td style="padding: 4px 0; font-size: 13px; color: #5B3924;">Tarikh:</td>
                                                <td align="right" style="padding: 4px 0; font-size: 13px; font-weight: 500; color: #5B3924;">{{ $booking->date->formatted_date }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 4px 0; font-size: 13px; color: #5B3924;">Masa:</td>
                                                <td align="right" style="padding: 4px 0; font-size: 13px; font-weight: 500; color: #5B3924;">{{ $booking->timeSlot->formatted_time }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 4px 0; font-size: 13px; color: #5B3924; vertical-align: top;">Meja:</td>
                                                <td align="right" style="padding: 4px 0; font-size: 13px; font-weight: 500; color: #5B3924;">
                                                    @foreach($booking->tableBookings as $tb)
                                                        <span style="display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 12px; background-color: #F5E6C6; margin-bottom: 2px;">{{ $tb->table->table_number }}</span>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        </table>
                                    </td>

                                    <!-- Spacer -->
                                    <td width="4%">&nbsp;</td>

                                    <!-- Customer Details -->
                                    <td width="48%" valign="top" style="background-color: #FFFFFF; border: 2px solid #5B3924; border-radius: 12px; padding: 16px;">
                                        <h2 style="margin: 0 0 12px 0; font-size: 14px; font-weight: 600; letter-spacing: 0.05em; color: #5B3924;">Maklumat Pelanggan</h2>
                                        <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td style="padding: 4px 0; font-size: 13px; color: #5B3924;">Nama:</td>
                                                <td align="right" style="padding: 4px 0; font-size: 13px; font-weight: 500; color: #5B3924;">{{ $booking->customer->name }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 4px 0; font-size: 13px; color: #5B3924;">Email:</td>
                                                <td align="right" style="padding: 4px 0; font-size: 13px; font-weight: 500; color: #5B3924; word-break: break-all;">{{ $booking->customer->email }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 4px 0; font-size: 13px; color: #5B3924;">Telefon:</td>
                                                <td align="right" style="padding: 4px 0; font-size: 13px; font-weight: 500; color: #5B3924;">{{ $booking->customer->phone_number }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Guest Breakdown Table -->
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background-color: #FFFFFF; border: 2px solid #5B3924; border-radius: 12px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 16px;">
                                        <h2 style="margin: 0 0 12px 0; font-size: 14px; font-weight: 600; letter-spacing: 0.05em; color: #5B3924;">Butiran Tetamu</h2>
                                        <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="color: #5B3924;">
                                            <!-- Table Header -->
                                            <tr style="border-bottom: 2px solid #5B3924;">
                                                <td style="padding: 8px 0; font-size: 13px; font-weight: 600; border-bottom: 2px solid #5B3924;">Kategori</td>
                                                <td align="center" style="padding: 8px 0; font-size: 13px; font-weight: 600; border-bottom: 2px solid #5B3924;">Bil.</td>
                                                <td align="right" style="padding: 8px 0; font-size: 13px; font-weight: 600; border-bottom: 2px solid #5B3924;">Harga</td>
                                                <td align="right" style="padding: 8px 0; font-size: 13px; font-weight: 600; border-bottom: 2px solid #5B3924;">Jumlah</td>
                                            </tr>
                                            <!-- Table Body -->
                                            @foreach($booking->details as $detail)
                                                <tr>
                                                    <td style="padding: 8px 0; font-size: 13px; border-bottom: 1px solid rgba(91, 57, 36, 0.2);">{{ $detail->price->category }}</td>
                                                    <td align="center" style="padding: 8px 0; font-size: 13px; border-bottom: 1px solid rgba(91, 57, 36, 0.2);">{{ $detail->quantity }}</td>
                                                    <td align="right" style="padding: 8px 0; font-size: 13px; border-bottom: 1px solid rgba(91, 57, 36, 0.2);">RM {{ number_format($detail->price->amount, 2) }}</td>
                                                    <td align="right" style="padding: 8px 0; font-size: 13px; border-bottom: 1px solid rgba(91, 57, 36, 0.2);">RM {{ number_format($detail->total, 2) }}</td>
                                                </tr>
                                            @endforeach
                                            <!-- Subtotal -->
                                            <tr>
                                                <td colspan="3" align="right" style="padding: 8px 0; font-size: 13px; border-bottom: 1px solid rgba(91, 57, 36, 0.2);">Subtotal:</td>
                                                <td align="right" style="padding: 8px 0; font-size: 13px; border-bottom: 1px solid rgba(91, 57, 36, 0.2);">RM {{ number_format($booking->subtotal, 2) }}</td>
                                            </tr>
                                            <!-- Discount -->
                                            @if($booking->discount > 0)
                                                <tr>
                                                    <td colspan="3" align="right" style="padding: 8px 0; font-size: 13px; border-bottom: 1px solid rgba(91, 57, 36, 0.2);">Diskaun:</td>
                                                    <td align="right" style="padding: 8px 0; font-size: 13px; border-bottom: 1px solid rgba(91, 57, 36, 0.2);">-RM {{ number_format($booking->discount, 2) }}</td>
                                                </tr>
                                            @endif
                                            <!-- Grand Total -->
                                            <tr>
                                                <td colspan="3" align="right" style="padding: 12px 0; font-size: 15px; font-weight: bold; color: #5B3924;">Jumlah Keseluruhan:</td>
                                                <td align="right" style="padding: 12px 0; font-size: 15px; font-weight: bold; color: #5B3924;">RM {{ number_format($booking->total, 2) }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Payment Information -->
                            @if($booking->status === \App\Models\Booking::STATUS_CONFIRMED && ($booking->transaction_reference_no || $booking->transaction_time))
                                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background-color: #FFFFFF; border: 2px solid #5B3924; border-radius: 12px; margin-bottom: 24px;">
                                    <tr>
                                        <td style="padding: 16px;">
                                            <h2 style="margin: 0 0 12px 0; font-size: 14px; font-weight: 600; letter-spacing: 0.05em; color: #5B3924;">Maklumat Pembayaran</h2>
                                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                                @if($booking->transaction_reference_no)
                                                    <tr>
                                                        <td style="padding: 4px 0; font-size: 13px; color: #5B3924;">Rujukan Transaksi:</td>
                                                        <td align="right" style="padding: 4px 0; font-size: 13px; font-weight: 500; color: #5B3924;">{{ $booking->transaction_reference_no }}</td>
                                                    </tr>
                                                @endif
                                                @if($booking->transaction_time)
                                                    <tr>
                                                        <td style="padding: 4px 0; font-size: 13px; color: #5B3924;">Masa Transaksi:</td>
                                                        <td align="right" style="padding: 4px 0; font-size: 13px; font-weight: 500; color: #5B3924;">{{ $booking->transaction_time->format('d M Y, g:i A') }}</td>
                                                    </tr>
                                                @endif
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            <!-- View Booking Online Button -->
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 8px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ url('/booking/' . $booking->reference_id) }}" style="display: inline-block; padding: 12px 32px; background-color: #5B3924; color: #faf5e8; font-size: 14px; font-weight: 600; text-decoration: none; border-radius: 8px; border: 2px solid #5B3924;">Lihat Tempahan Dalam Talian</a>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                </table>

                <!-- Footer -->
                <table role="presentation" cellpadding="0" cellspacing="0" width="600" style="max-width: 600px; width: 100%;">
                    <tr>
                        <td align="center" style="padding: 24px 0;">
                            <p style="margin: 0; font-size: 12px; color: #5B3924;">Sila simpan rujukan tempahan ini untuk rekod anda.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
