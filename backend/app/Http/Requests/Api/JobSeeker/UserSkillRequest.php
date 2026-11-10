<?php

namespace App\Http\Requests\Api\JobSeeker;

use Illuminate\Foundation\Http\FormRequest;

class UserSkillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                $this->isMethod('POST') ? 'required' : 'sometimes',
                'string',
                'min:2',
                'max:100',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Skill name is required.',
            'name.string' => 'Skill name must be text.',
            'name.min' => 'Skill name must be at least 2 characters.',
            'name.max' => 'Skill name cannot exceed 100 characters.',
        ];
    }
}
