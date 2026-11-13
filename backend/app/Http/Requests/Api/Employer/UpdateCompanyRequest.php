<?php

namespace App\Http\Requests\Api\Employer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],

            'founded_year' => [
                'required',
                'integer',
                'min:1800',
                'max:' . date('Y'),
            ],

            'employee_count' => ['required', 'string', 'max:255'],
            'industry' => ['required', 'string', 'max:255'],
            'website' => ['required', 'url', 'max:255'],
            'company_email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string'],
            'city' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],

            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'cover_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'gallery' => ['nullable', 'array', 'max:10'],

            'gallery.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'existing_gallery' => ['sometimes', 'array', 'max:10'],
            'existing_gallery.*' => ['string', 'url'],
        ];
    }
}