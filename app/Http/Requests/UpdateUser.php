<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUser extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('manage-users');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $this->route('user')],
            'password' => ['sometimes', 'string', 'min:8', 'confirmed'],
            'student_id' => ['nullable', 'string', 'max:50', 'unique:users,student_id,' . $this->route('user')],
            'employee_id' => ['nullable', 'string', 'max:50', 'unique:users,employee_id,' . $this->route('user')],
            'phone' => ['nullable', 'string', 'max:20'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'organization_id' => ['nullable', 'exists:organizations,id'],
            'role' => ['sometimes', 'exists:roles,name'],
        ];
    }
}
