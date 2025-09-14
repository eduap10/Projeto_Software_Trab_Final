<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\Task;
use App\Observers\TaskObserver;
use App\Services\GameService;
use App\Services\AchievementService;
use App\Contracts\TaskFactoryInterface;
use App\Factories\TaskFactory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Singleton explícito para GameService
        $this->app->singleton(GameService::class, function ($app) {
            return new GameService($app->make(AchievementService::class));
        });
        // DIP: Interface → Implementação (Factory Pattern)
        $this->app->bind(TaskFactoryInterface::class, TaskFactory::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Observer Pattern no modelo Task
        Task::observe(TaskObserver::class);
    }
}
