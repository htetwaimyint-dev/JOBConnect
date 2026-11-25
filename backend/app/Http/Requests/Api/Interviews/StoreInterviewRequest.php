<?php

namespace App\Http\Requests\Api\Interviews;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInterviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'application_id' => [
                'required',
                'integer',
                'exists:applications,id',
                Rule::unique('interviews', 'application_id'),
            ],
            'interview_type' => ['required', Rule::in(['online', 'onsite', 'phone'])],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'location' => ['nullable', 'required_if:interview_type,onsite', 'string', 'max:255'],
            'meeting_link' => ['nullable', 'required_if:interview_type,online', 'url', 'max:255'],
            'notes' => ['nullable', 'string'],
            'interviewer_ids' => ['sometimes', 'array'],
            'interviewer_ids.*' => ['integer', 'distinct', 'exists:interviewers,id'],
        ];
    }
}
