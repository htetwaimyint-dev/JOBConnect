<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\Contracts\ResumeRepositoryInterface;
use App\Repositories\Eloquent\ResumeRepository;

use App\Repositories\Contracts\JobSeekerProfileRepositoryInterface;
use App\Repositories\Eloquent\JobSeekerProfileRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ResumeRepositoryInterface::class,
            ResumeRepository::class
        );

        $this->app->bind(
            JobSeekerProfileRepositoryInterface::class,
            JobSeekerProfileRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}
