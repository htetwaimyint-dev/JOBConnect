<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'verification_status' => [
                'required',
                Rule::in(['verified', 'rejected']),
            ],

            'rejection_reason' => [
                'required_if:verification_status,rejected',
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }
}
