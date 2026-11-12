<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\Contracts\ResumeRepositoryInterface;
use App\Repositories\Eloquent\ResumeRepository;

use App\Repositories\Contracts\JobSeekerProfileRepositoryInterface;
use App\Repositories\Eloquent\JobSeekerProfileRepository;

use App\Repositories\Contracts\CompanyRepositoryInterface;
use App\Repositories\Eloquent\CompanyRepository;

use App\Repositories\Contracts\SkillRepositoryInterface;
use App\Repositories\Eloquent\SkillRepository;

use App\Repositories\Contracts\JobRepositoryInterface;
use App\Repositories\Eloquent\JobRepository;

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
        $this->app->bind(
            SkillRepositoryInterface::class,
            SkillRepository::class
        );
        $this->app->bind(
            JobRepositoryInterface::class,
            JobRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}