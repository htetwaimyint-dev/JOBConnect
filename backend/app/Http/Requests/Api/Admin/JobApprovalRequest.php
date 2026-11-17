<?php

namespace App\Http\Requests\Api\Admin;

use App\Enums\JobStatus;
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
                Rule::in([
                    JobStatus::Published->value,
                    JobStatus::Rejected->value,
                ]),
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
