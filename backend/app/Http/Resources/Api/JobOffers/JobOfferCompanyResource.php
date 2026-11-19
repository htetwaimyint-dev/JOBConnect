<?php

namespace App\Http\Resources\Api\JobOffers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class JobOfferCompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $logo = $this->logo;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'logo' => $logo
                ? (Str::startsWith($logo, ['http://', 'https://']) ? $logo : Storage::disk('public')->url($logo))
                : null,
        ];
    }
}
