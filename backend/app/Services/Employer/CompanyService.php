<?php

namespace App\Services;

use App\Models\Company;

class CompanyService
{
    /**
     * Get company profile by employer ID.
     */
    public function getCompanyByEmployerId(int $employerId): ?Company
    {
        return Company::where('employer_id', $employerId)->first();
    }
}