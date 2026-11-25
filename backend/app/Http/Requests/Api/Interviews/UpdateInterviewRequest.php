<?php

namespace App\Http\Requests\Api\Interviews;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInterviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'interview_type' => ['sometimes', 'required', Rule::in(['online', 'onsite', 'phone'])],
            'scheduled_at' => ['required_if:status,rescheduled', 'date', 'after:now'],
            'status' => ['sometimes', Rule::in(['rescheduled'])],
            'location' => ['sometimes', 'nullable', 'string', 'max:255'],
            'meeting_link' => ['sometimes', 'nullable', 'url', 'max:255'],
            'notes' => ['sometimes', 'nullable', 'string'],
            'interviewer_ids' => ['sometimes', 'array'],
            'interviewer_ids.*' => ['integer', 'distinct', 'exists:interviewers,id'],
        ];
    }
}
