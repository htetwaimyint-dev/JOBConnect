<?php

namespace App\Http\Requests\Api\Interviews;

use Illuminate\Foundation\Http\FormRequest;

class StoreInterviewerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
        ];
    }
}
