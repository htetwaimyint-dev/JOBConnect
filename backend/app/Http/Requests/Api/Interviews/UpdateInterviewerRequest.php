<?php

namespace App\Http\Requests\Api\Interviews;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInterviewerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'position' => ['sometimes', 'required', 'string', 'max:255'],
        ];
    }
}
