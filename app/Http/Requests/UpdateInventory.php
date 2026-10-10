<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateInventory extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('manage-inventory');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'equipment_id' => ['sometimes', 'exists:equipment,id'],
            'quantity' => ['sometimes', 'integer', 'min:0'],
            'location' => ['sometimes', 'string', 'max:255'],
            'condition' => ['sometimes', 'in:good,fair,poor,damaged'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
