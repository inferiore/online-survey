<?php

namespace App\Providers;

use App\Contracts\QuestionRepositoryInterface;
use App\Contracts\QuestionServiceInterface;
use App\Contracts\SurveyRepositoryInterface;
use App\Contracts\SurveyResponseServiceInterface;
use App\Contracts\SurveyServiceInterface;
use App\Repositories\QuestionRepository;
use App\Repositories\SurveyRepository;
use App\Services\QuestionService;
use App\Services\SurveyResponseService;
use App\Services\SurveyService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Repository Bindings
        $this->app->bind(
            SurveyRepositoryInterface::class,
            SurveyRepository::class
        );

        $this->app->bind(
            QuestionRepositoryInterface::class,
            QuestionRepository::class
        );

        // Service Bindings
        $this->app->bind(
            SurveyServiceInterface::class,
            SurveyService::class
        );

        $this->app->bind(
            SurveyResponseServiceInterface::class,
            SurveyResponseService::class
        );

        $this->app->bind(
            QuestionServiceInterface::class,
            QuestionService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
