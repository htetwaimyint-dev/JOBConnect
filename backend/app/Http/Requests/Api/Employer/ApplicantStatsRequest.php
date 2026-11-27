<?php

namespace App\Http\Requests\Api\Employer;

use Illuminate\Foundation\Http\FormRequest;

class ApplicantStatsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // public function rules(): array
    // {
    //     return [
    //         'month' => ['required', 'integer', 'between:1,12'],
    //         'year' => ['required', 'integer', 'min:2020', 'max:2100'],
    //     ];
    // }
}