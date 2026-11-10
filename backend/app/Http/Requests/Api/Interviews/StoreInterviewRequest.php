<?php

namespace App\Http\Requests\Api\Interviews;

use Illuminate\Foundation\Http\FormRequest;

class StoreInterviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null; //login user access
    }

    public function rules(): array
    {
        return [
            'application_id' => [
                'required',
                'integer',
                'exists:applications,id',
                'unique:interviews,application_id',
            ],
            'interview_type' => [
                'required',
                'in:online,onsite,phone',
            ],
            'scheduled_at' => [
                'required',
                'date',
                'after:now',
            ],
            'location' => ['nullable', 'string', 'max:255'],
            'meeting_link' => ['nullable', 'url', 'max:255'],
            'notes' => ['nullable', 'string'],
            'status' => [
                'sometimes',
                'in:scheduled,completed,cancelled,rescheduled',
            ],
        ];
    }
}
