<?php

namespace App\Http\Requests\Api\JobSeeker;

use Illuminate\Foundation\Http\FormRequest;

class EducationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'institution' => $this->isMethod('POST')
                ? ['required', 'string', 'max:255']
                : ['sometimes', 'required', 'string', 'max:255'],

            'degree' => ['sometimes', 'nullable', 'string', 'max:255'],

            'field_of_study' => ['sometimes', 'nullable', 'string', 'max:255'],

            'start_date' => ['sometimes', 'nullable', 'date'],

            'end_date' => ['sometimes', 'nullable', 'date', 'after_or_equal:start_date'],

            'description' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
