<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFacilityReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'facility_id' => ['required', 'exists:facilities,id'],
            'reservee_name' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'agreement' => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'facility_id.required' => 'Please select a facility to reserve.',
            'reservee_name.required' => 'Please provide the reservee name.',
            'contact_number.required' => 'Please provide a contact number.',
            'start_date.after' => 'The start date must be after today.',
            'end_date.after' => 'The end date must be after the start date.',
            'agreement.required' => 'You must agree to the terms and conditions.',
        ];
    }
}
