<?php

namespace App\Http\Requests\Api\JobSeeker;

use Illuminate\Foundation\Http\FormRequest;

class ResumeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') ||
            $this->isMethod('PATCH');

        return [
            'title' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                'max:255',
            ],

            'file' => [
                $isUpdate ? 'sometimes' : 'required',
                'file',
                'mimes:pdf,doc,docx',
                'max:5120',
            ],

            'is_default' => [
                'sometimes',
                'boolean',
            ],

            'visibility' => [
                'sometimes',
                'in:private,public',
            ],
        ];
    }
}
