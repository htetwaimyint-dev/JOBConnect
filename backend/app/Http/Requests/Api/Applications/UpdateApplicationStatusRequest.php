<?php

namespace App\Http\Requests\Api\Applications;

use App\Enums\ApplicationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateApplicationStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'employer';
    }

    public function rules(): array
    {
        $allowedStatuses = [
            ApplicationStatus::Reviewed->value,
            ApplicationStatus::Shortlisted->value,
            ApplicationStatus::Interview->value,
            ApplicationStatus::Accepted->value,
            ApplicationStatus::Rejected->value,
        ];

        return [
            'new_status' => [
                'required',
                'string',
                Rule::in($allowedStatuses),
            ],
            'note' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'new_status.required' => 'Please select an application status.',
            'new_status.in' => 'The selected application status is invalid.',
            'note.max' => 'The note may not exceed 1000 characters.',
        ];
    }
}
