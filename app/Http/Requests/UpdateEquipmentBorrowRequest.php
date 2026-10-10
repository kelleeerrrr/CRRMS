<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEquipmentBorrowRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('view-equipment');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'organization_id' => ['sometimes', 'exists:organizations,id'],
            'purpose' => ['sometimes', 'string', 'max:255'],
            'start_date' => ['sometimes', 'date', 'after:today'],
            'end_date' => ['sometimes', 'date', 'after:start_date'],
        ];
    }
}
