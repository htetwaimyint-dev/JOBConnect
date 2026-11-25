<?php

namespace App\Http\Requests\Api\Interviews;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JobseekerRequestRescheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'scheduled_at' => ['nullable', 'date', 'after:now'],
            'reason' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }
}
