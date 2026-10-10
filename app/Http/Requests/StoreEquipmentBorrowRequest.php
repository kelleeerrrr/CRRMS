<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEquipmentBorrowRequest extends FormRequest
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
            'equipment_id' => ['required', 'exists:equipment,id'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'quantity' => ['required', 'integer', 'min:1', function ($attribute, $value, $fail) {
                $equipmentId = request()->input('equipment_id');
                $equipment = \App\Models\Equipment::find($equipmentId);
                
                if ($equipment) {
                    // Check if equipment is available for borrowing
                    if (!$equipment->isAvailableForBorrowing()) {
                        $fail('This equipment is not available for borrowing.');
                        return;
                    }
                    
                    // Check if requested quantity exceeds available quantity
                    $availableQuantity = $equipment->getAvailableQuantityAttribute();
                    if ($value > $availableQuantity) {
                        $fail("Only {$availableQuantity} units are available. You requested {$value}.");
                    }
                }
            }],
            'borrow_date' => ['required', 'date', 'after:now'],
            'return_date' => ['required', 'date', 'after:borrow_date'],
            'purpose' => ['required', 'string', 'max:255'],
            'intended_use' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'agreement' => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'equipment_id.required' => 'Please select an equipment.',
            'quantity.required' => 'Please specify the quantity to borrow.',
            'quantity.min' => 'Quantity must be at least 1.',
            'borrow_date.after' => 'The borrow date must be in the future.',
            'return_date.after' => 'The return date must be after the borrow date.',
            'purpose.required' => 'Please provide a purpose for the borrow request.',
            'agreement.required' => 'You must agree to the borrowing terms.',
            'agreement.accepted' => 'You must agree to the borrowing terms.',
        ];
    }
}
