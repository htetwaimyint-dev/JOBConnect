<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\Contracts\ResumeRepositoryInterface;
use App\Repositories\Eloquent\ResumeRepository;

use App\Repositories\Contracts\JobSeekerProfileRepositoryInterface;
use App\Repositories\Eloquent\JobSeekerProfileRepository;

use App\Repositories\Contracts\CompanyRepositoryInterface;
use App\Repositories\Eloquent\CompanyRepository;

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

        $this->app->bind(
            CompanyRepositoryInterface::class,
            CompanyRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}