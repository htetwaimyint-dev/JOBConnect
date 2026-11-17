<?php

namespace App\Http\Requests\Api\Jobs;

use App\Enums\JobStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJobRequest extends FormRequest
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

            'country' => [
                'nullable',
                'string',
                'max:255',
            ],

            'city' => [
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
            ],

            'gender' => [
                'required',
                'in:any,male,female',
            ],

            'vacancies' => [
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
                Rule::in([
                    JobStatus::Draft->value,
                    JobStatus::Pending->value,
                ]),
            ],

            'salary_type' => [
                'required',
                'in:fixed,negotiable',
            ],
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

            'deadline.after_or_equal' =>
            'The application deadline cannot be in the past.',
        ];
    }
}
