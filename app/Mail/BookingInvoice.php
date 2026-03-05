<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingInvoice extends Mailable
{
    use SerializesModels;

    public function __construct(public Booking $booking) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Pengesahan Tempahan #{$this->booking->reference_id} - Sand Village",
        );
    }

    public function content(): Content
    {
        $this->booking->load(['customer', 'date', 'timeSlot', 'details.price', 'tableBookings.table']);

        return new Content(
            view: 'emails.booking-invoice',
            text: 'emails.booking-invoice-text',
        );
    }
}
