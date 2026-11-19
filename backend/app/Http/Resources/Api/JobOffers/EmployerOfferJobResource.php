<?php

namespace App\Http\Resources\Api\JobOffers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployerOfferJobResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'employment_type' => $this->employment_type,
            'workplace_type' => $this->workplace_type,
            'country' => $this->country,
            'city' => $this->city,
            'salary_min' => $this->salary_min,
            'salary_max' => $this->salary_max,
            'salary_type' => $this->salary_type,
        ];
    }
}
