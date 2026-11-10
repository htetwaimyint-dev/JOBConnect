<?php

namespace App\Http\Requests\Api\JobSeeker;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => ['sometimes', 'nullable', 'string', 'max:50'],

            'date_of_birth' => [
                'sometimes',
                'nullable',
                'date',
                'before_or_equal:today',
            ],
            'profile_photo' => [
                'sometimes',
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'gender' => ['sometimes', 'nullable', 'string', 'max:50'],

            'address' => ['sometimes', 'nullable', 'string'],

            'city' => ['sometimes', 'nullable', 'string', 'max:100'],

            'professional_title' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'bio' => ['sometimes', 'nullable', 'string'],

            'expected_salary' => [
                'sometimes',
                'nullable',
                'numeric',
                'min:0',
                'max:9999999999.99',
            ],

            'experience_years' => [
                'sometimes',
                'nullable',
                'numeric',
                'min:0',
                'max:999.9',
            ],

            'linkedin_url' => [
                'sometimes',
                'nullable',
                'url',
                'max:255',
            ],

            'portfolio_url' => [
                'sometimes',
                'nullable',
                'url',
                'max:255',
            ],
        ];
    }
}
