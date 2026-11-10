<?php

namespace App\Http\Requests\Api\JobSeeker;

use Illuminate\Foundation\Http\FormRequest;

class ExperienceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'job_title' => $this->isMethod('POST')
                ? ['required', 'string', 'max:255']
                : ['sometimes', 'required', 'string', 'max:255'],

            'company_name' => $this->isMethod('POST')
                ? ['required', 'string', 'max:255']
                : ['sometimes', 'required', 'string', 'max:255'],

            'employment_type' => [
                'sometimes',
                'nullable',
                'string',
                'max:100',
            ],

            'start_date' => [
                'sometimes',
                'nullable',
                'date',
            ],

            'end_date' => [
                'sometimes',
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],

            'is_current' => [
                'sometimes',
                'boolean',
            ],

            'description' => [
                'sometimes',
                'nullable',
                'string',
            ],
        ];
    }
}
