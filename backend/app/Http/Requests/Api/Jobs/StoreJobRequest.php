<?php

namespace App\Http\Requests\Api\Jobs;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'category_id' => [
                'nullable',
                'integer',
                'exists:categories,id',
            ],

            'description' => [
                'required',
                'string',
            ],

            'requirements' => [
                'nullable',
                'string',
            ],

            'responsibilities' => [
                'nullable',
                'string',
            ],

            'employment_type' => [
                'required',
                'in:full_time,part_time,contract,internship',
            ],

            'workplace_type' => [
                'required',
                'in:onsite,remote,hybrid',
            ],

            'location' => [
                'nullable',
                'string',
                'max:255',
            ],

            'salary_min' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'salary_max' => [
                'nullable',
                'numeric',
                'min:0',
                'gte:salary_min',
            ],

            'vacancies' => [
                'sometimes',
                'required',
                'integer',
                'min:1',
            ],

            'deadline' => [
                'nullable',
                'date',
                'after_or_equal:today',
            ],

            'submission_status' => [
                'sometimes',
                'required',
                'in:draft,pending',
            ],

            'salary_type' => 'required|in:fixed,negotiable',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Please enter a job title.',

            'description.required' =>
            'Please provide a job description.',

            'category_id.exists' =>
            'The selected job category does not exist.',

            'salary_max.gte' =>
            'Maximum salary must be greater than or equal to minimum salary.',

            'deadline.after_or_equal' =>
            'The application deadline cannot be in the past.',

            'submission_status.in' =>
            'The submission status must be draft or pending.',
        ];
    }
}
