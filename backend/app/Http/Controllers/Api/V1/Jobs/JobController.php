<?php

namespace App\Http\Controllers\Api\V1\Jobs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Jobs\PublicJobIndexRequest;
use App\Http\Resources\Api\Jobs\JobResource;
use App\Services\Jobs\JobService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class JobController extends Controller
{
    protected JobService $jobService;

    public function __construct(JobService $jobService)
    {
        $this->jobService = $jobService;
    }

    /**
     * Display published jobs for public visitors.
     */
    public function index(
        PublicJobIndexRequest $request
    ): AnonymousResourceCollection {
        $jobs = $this->jobService->getPublishedJobs(
            $request->validated(),
            10
        );

        return JobResource::collection($jobs);
    }

    /**
     * Display a single published job.
     */
    public function show(string $id): JobResource
    {
        $job = $this->jobService->getPublishedJob((int) $id);

        return new JobResource($job);
    }
}
