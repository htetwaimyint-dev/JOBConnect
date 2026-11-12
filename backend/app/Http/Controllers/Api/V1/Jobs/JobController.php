<?php

namespace App\Http\Controllers\Api\V1\Jobs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Jobs\StoreCategoryRequest;
use App\Http\Requests\Api\Jobs\StoreJobRequest;
use App\Http\Resources\Api\JobResource;
use App\Services\Employer\CompanyService;
use App\Services\Jobs\JobService;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;

class JobController extends Controller
{
    protected JobService $jobService;
    protected CompanyService $companyService;

    public function __construct(
        JobService $jobService,
        CompanyService $companyService
    ) {
        $this->jobService = $jobService;
        $this->companyService = $companyService;
    }

    public function index(Request $request)
    {
        $company = $this->getEmployerCompany($request->user());

        $perPage = min(
            max((int) $request->query('perpage', 10), 1),
            50
        );

        $job = $this->jobService->getjobs($company, $perPage);

        return response()->json([
            'success' => true,
            'message' => 'Jobs retrieved successfully.',
            'data' => new JobResource($job),
        ]);
    }

    public function store(StoreJobRequest $request)
    {
        $company = $this->getEmployerCompany($request->user());

        $job = $this->jobService->createJob($company, $request->validated());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    private function getEmployerCompany(User $user): Company
    {
        $company = $this->companyService->getCompanyByEmployer($user);

        abort_unless(
            $company,
            403,
            'Please create your company profile before posting a job.'
        );

        return $company;
    }
}
