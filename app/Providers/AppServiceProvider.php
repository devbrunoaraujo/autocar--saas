<?php

namespace App\Providers;

use App\Contracts\FipeServiceInterface;
use App\Contracts\ImageProcessorInterface;
use App\Services\FipeService;
use App\Services\ImageProcessorService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(FipeServiceInterface::class, FipeService::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
