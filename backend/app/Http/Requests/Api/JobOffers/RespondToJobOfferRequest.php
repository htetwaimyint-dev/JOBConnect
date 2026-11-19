<?php

namespace App\Http\Requests\Api\JobOffers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RespondToJobOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        // The service scopes the offer to the authenticated job seeker.
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(['accepted', 'rejected'])],
        ];
    }
}
