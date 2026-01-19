<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date_id' => 'required|exists:dates,id',
            'time_slot_id' => 'required|exists:time_slots,id',
            'total_pax' => 'required|integer|min:1|max:192',
        ];
    }

    public function messages(): array
    {
        return [
            'date_id.required' => 'Please select a date.',
            'date_id.exists' => 'The selected date is not available.',
            'time_slot_id.required' => 'Please select a time slot.',
            'time_slot_id.exists' => 'The selected time slot is not available.',
            'total_pax.required' => 'Please specify the number of guests.',
            'total_pax.min' => 'At least 1 guest is required.',
            'total_pax.max' => 'Maximum capacity is 192 guests.',
        ];
    }
}
