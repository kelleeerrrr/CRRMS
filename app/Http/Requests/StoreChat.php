<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreChat extends FormRequest
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
            'type' => ['required', 'in:private,group'],
            'name' => ['required_if:type,group', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'participant_id' => ['required_if:type,private', 'exists:users,id'],
            'participants' => ['required_if:type,group', 'array'],
            'participants.*' => ['exists:users,id'],
            'message' => ['nullable', 'string'],
        ];
    }
}
