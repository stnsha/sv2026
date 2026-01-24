<?php

namespace App\Http\Requests\Admin;

use App\Models\Table;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AmendBookingRequest extends FormRequest
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
            'table_ids' => 'nullable|array',
            'table_ids.*' => 'exists:tables,id',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $this->validateTableCapacity($validator);
            },
        ];
    }

    private function validateTableCapacity(Validator $validator): void
    {
        $tableIds = $this->input('table_ids');

        if (empty($tableIds)) {
            return;
        }

        $booking = $this->route('booking');
        $booking->loadMissing('details');

        $requiredPax = $booking->details->sum('quantity');
        $selectedCapacity = Table::whereIn('id', $tableIds)->sum('capacity');

        if ($selectedCapacity < $requiredPax) {
            $validator->errors()->add(
                'table_ids',
                "Selected tables have insufficient capacity. Need {$requiredPax} pax, selected {$selectedCapacity}."
            );
        }
    }

    public function messages(): array
    {
        return [
            'date_id.required' => 'Please select a date.',
            'date_id.exists' => 'The selected date is not available.',
            'time_slot_id.required' => 'Please select a time slot.',
            'time_slot_id.exists' => 'The selected time slot is not available.',
            'table_ids.array' => 'Invalid table selection format.',
            'table_ids.*.exists' => 'One or more selected tables do not exist.',
        ];
    }
}
