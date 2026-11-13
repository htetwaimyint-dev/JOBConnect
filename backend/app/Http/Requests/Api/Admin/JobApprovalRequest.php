<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JobApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::in(['published', 'rejected']),
            ],

            'rejection_reason' => [
                'required_if:status,rejected',
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }
}
