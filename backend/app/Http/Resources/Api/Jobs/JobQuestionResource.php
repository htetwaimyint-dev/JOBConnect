<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobQuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'question' => $this->question,

            'is_required' => (bool) $this->is_required,

            'sort_order' => $this->sort_order,
        ];
    }
}
