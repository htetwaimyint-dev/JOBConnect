<?php

namespace App\Http\Requests\Api\Jobs;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PublicJobIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => [
                'nullable',
                'string',
                'max:255',
            ],

            'category_id' => [
                'nullable',
                'integer',
                'exists:categories,id',
            ],

            'employment_type' => [
                'nullable',
                Rule::in([
                    'full_time',
                    'part_time',
                    'contract',
                    'internship',
                ]),
            ],

            'workplace_type' => [
                'nullable',
                Rule::in([
                    'onsite',
                    'remote',
                    'hybrid',
                ]),
            ],

            'location' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }
}
