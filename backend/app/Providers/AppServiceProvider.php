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

use App\Repositories\Contracts\ApplicationRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\JobQuestionRepositoryInterface;
use App\Repositories\Eloquent\ApplicationRepository;
use App\Repositories\Eloquent\CategoryRepository;
use App\Repositories\Eloquent\JobQuestionRepository;

use App\Repositories\Contracts\SavedJobRepositoryInterface;
use App\Repositories\Eloquent\SavedJobRepository;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Eloquent\CategoryRepository;

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
        $this->app->bind(
            CategoryRepositoryInterface::class,
            CategoryRepository::class
        );
        $this->app->bind(
            ApplicationRepositoryInterface::class,
            ApplicationRepository::class
        );
        $this->app->bind(
            JobQuestionRepositoryInterface::class,
            JobQuestionRepository::class
        );
        $this->app->bind(
            SavedJobRepositoryInterface::class,
            SavedJobRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}
