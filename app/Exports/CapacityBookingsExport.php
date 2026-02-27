<?php

namespace App\Exports;

use App\Models\Booking;
use App\Models\Date;
use App\Models\TimeSlot;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CapacityBookingsExport implements FromCollection, WithHeadings
{
    public function __construct(
        private Date $date,
        private TimeSlot $timeSlot
    ) {}

    public function collection(): Collection
    {
        return Booking::query()
            ->where('date_id', $this->date->id)
            ->where('time_slot_id', $this->timeSlot->id)
            ->where('status', Booking::STATUS_CONFIRMED)
            ->with(['customer', 'details.price', 'tableBookings.table'])
            ->get()
            ->map(function (Booking $booking) {
                $quantities = $this->getQuantitiesByCategory($booking);
                $tables = $this->getTableNumbers($booking);

                return [
                    'customer_name'         => $booking->customer->name ?? '',
                    'reference_id'          => $booking->reference_id,
                    'date_booked'           => $this->date->date_value->format('Y-m-d'),
                    'time_slot'             => $this->timeSlot->formatted_time,
                    'dewasa'                => $quantities['dewasa'],
                    'warga_emas'            => $quantities['warga_emas'],
                    'kanak_kanak_5_atas'    => $quantities['kanak_kanak_5_atas'],
                    'kanak_kanak_4_bawah'   => $quantities['kanak_kanak_4_bawah'],
                    'table'                 => $tables,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Customer Name',
            'Reference No',
            'Date Booked',
            'Time Slot',
            'Dewasa',
            'Warga Emas',
            'Kanak-kanak (5 tahun ke atas)',
            'Kanak-kanak (4 tahun ke bawah)',
            'Table',
        ];
    }

    private function getQuantitiesByCategory(Booking $booking): array
    {
        $quantities = [
            'dewasa'              => 0,
            'warga_emas'          => 0,
            'kanak_kanak_5_atas'  => 0,
            'kanak_kanak_4_bawah' => 0,
        ];

        foreach ($booking->details as $detail) {
            $price = $detail->price;

            if ($price === null) {
                continue;
            }

            if ($price->category === 'Dewasa') {
                $quantities['dewasa'] += (int) $detail->quantity;
            } elseif ($price->category === 'Warga Emas') {
                $quantities['warga_emas'] += (int) $detail->quantity;
            } elseif ($price->category === 'Kanak-kanak' && ! $price->extra_chair) {
                $quantities['kanak_kanak_5_atas'] += (int) $detail->quantity;
            } elseif ($price->category === 'Kanak-kanak' && $price->extra_chair) {
                $quantities['kanak_kanak_4_bawah'] += (int) $detail->quantity;
            }
        }

        return $quantities;
    }

    private function getTableNumbers(Booking $booking): string
    {
        return $booking->tableBookings
            ->map(fn ($tb) => $tb->table?->table_number)
            ->filter()
            ->sort()
            ->implode(', ');
    }
}
