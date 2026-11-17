<?php

namespace App\Http\Requests\Api\Applications;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\JobStatus;
use App\Enums\UserRole;

class StoreApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::JOB_SEEKER;
    }

    public function rules(): array
    {
        return [
            'job_id' => [
                'required',
                'integer',
                Rule::exists('jobs', 'id')->where(function ($query) {
                    $query->where('status', JobStatus::Published->value);
                }),
                Rule::unique('applications', 'job_id')
                    ->where(function ($query) {
                        $query->where(
                            'job_seeker_id',
                            $this->user()->id
                        );
                    }),
            ],

            'applicant_name' => [
                'required',
                'string',
                'max:255',
            ],

            'applicant_phone' => [
                'required',
                'string',
                'max:50',
            ],

            'resume_id' => [
                'required',
                'integer',
                Rule::exists('resumes', 'id')->where(function ($query) {
                    $query->where('job_seeker_id', $this->user()->id);
                }),
            ],

            'answers' => [
                'sometimes',
                'array',
            ],

            'answers.*.job_question_id' => [
                'required',
                'integer',
                'distinct',
                'exists:job_questions,id',
            ],

            'answers.*.answer' => [
                'required',
                'string',
                'max:5000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'job_id.exists' => 'The selected job is not available.',
            'resume_id.exists' => 'The selected resume does not belong to you.',
            'job_id.unique' => 'You have already applied for this job.',
            'answers.*.job_question_id.exists' => 'The selected question does not exist.',
            'answers.*.job_question_id.distinct' => 'You cannot answer the same question more than once.',
            'answers.*.answer.required' => 'Please answer all submitted questions.',
        ];
    }
}
