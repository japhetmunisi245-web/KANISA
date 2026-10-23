<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
            'dob' => ['nullable', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:500'],
            'street_name' => ['nullable', 'string', 'max:255'],
            'ward' => ['nullable', 'string', 'max:255'],
            'marital_status' => ['nullable', Rule::in(['single', 'married', 'widowed'])],
            'spouse_names' => ['nullable', 'string', 'max:255', 'required_if:marital_status,married'],
            'children_count' => ['nullable', 'integer', 'min:0', 'max:50'],
            'saved_date' => ['nullable', 'date', 'before_or_equal:today'],
            'baptism_date' => ['nullable', 'date', 'before_or_equal:today'],
            'education' => ['nullable', 'string', 'max:255'],
            'service' => ['nullable', 'string', 'max:255'],
            'department_ids' => ['nullable', 'array', 'max:5'],
            'department_ids.*' => ['integer', 'distinct', 'exists:departments,id'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'language' => ['nullable', Rule::in(['sw', 'en'])],
        ];
    }
}
