<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateResourceBorrowRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'quantity' => 'required|integer|min:1',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'purpose' => 'required|string|max:500',
            'intended_use' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'contact_number' => 'required|string|max:20',
            'email' => 'required|email',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'quantity.required' => 'Please specify the quantity to borrow.',
            'quantity.min' => 'Quantity must be at least 1.',
            'start_date.required' => 'Please specify the borrow date and time.',
            'start_date.after' => 'Borrow date must be in the future.',
            'end_date.required' => 'Please specify the return date and time.',
            'end_date.after' => 'Return date must be after the borrow date.',
            'purpose.required' => 'Please specify the purpose of borrowing.',
            'contact_number.required' => 'Please provide a contact number.',
            'email.required' => 'Please provide your email address.',
            'email.email' => 'Please provide a valid email address.',
        ];
    }
}
