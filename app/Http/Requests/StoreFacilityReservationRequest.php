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
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'organization_id' => ['nullable', 'exists:organizations,id'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'attendees_count' => ['nullable', 'integer', 'min:1'],
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
            'end_time.after' => 'The end time must be after the start time.',
        ];
    }
}
