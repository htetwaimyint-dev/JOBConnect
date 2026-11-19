<?php

namespace App\Http\Requests\Api\JobOffers;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        // The route is protected by auth:sanctum and the existing employer middleware.
        return true;
    }

    public function rules(): array
    {
        return [
            'job_seeker_id' => ['required', 'integer', 'exists:users,id'],
            'job_id' => ['required', 'integer', 'exists:jobs,id'],
            'salary_offered' => ['required', 'numeric', 'gt:0'],
            'offer_details' => ['nullable', 'string', 'max:5000'],
            'expires_at' => ['required', 'date', 'after:now'],
        ];
    }
}
