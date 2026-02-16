<?php

namespace App\Http\Requests;

use App\Services\CapacityService;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer.name' => 'required|string|max:255',
            'customer.email' => 'required|email|max:255',
            'customer.phone_number' => 'required|string|max:20',
            'date_id' => 'required|exists:dates,id',
            'time_slot_id' => 'required|exists:time_slots,id',
            'pax_details' => 'required|array|min:1',
            'pax_details.*.price_id' => 'required|exists:prices,id',
            'pax_details.*.quantity' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'customer.name.required' => 'Please enter your name.',
            'customer.email.required' => 'Please enter your email address.',
            'customer.email.email' => 'Please enter a valid email address.',
            'customer.phone_number.required' => 'Please enter your phone number.',
            'date_id.required' => 'Please select a date.',
            'date_id.exists' => 'The selected date is not available.',
            'time_slot_id.required' => 'Please select a time slot.',
            'time_slot_id.exists' => 'The selected time slot is not available.',
            'pax_details.required' => 'Please specify the number of guests.',
            'pax_details.min' => 'At least one guest category is required.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->any()) {
                return;
            }

            $paxDetails = $this->input('pax_details', []);
            $totalPax = collect($paxDetails)->sum('quantity');

            $capacityService = app(CapacityService::class);
            $minimumPax = $capacityService->getEffectiveMinimumPax(
                (int) $this->input('date_id'),
                (int) $this->input('time_slot_id')
            );

            if ($totalPax < $minimumPax) {
                $validator->errors()->add(
                    'pax_details',
                    "Minimum booking size for this time slot is {$minimumPax} guests."
                );
            }

            if ($totalPax > 192) {
                $validator->errors()->add('pax_details', 'Maximum capacity is 192 guests.');
            }
        });
    }
}
