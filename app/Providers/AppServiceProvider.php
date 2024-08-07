<?php

namespace App\Providers;

use App\Repositories\BaseRepository;
use App\Repositories\BookingRepository;
use App\Repositories\ClinicsRepository;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Repositories\ServiceRepository;
use App\Repositories\UserProfileRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BaseRepositoryInterface::class,UserRepository::class);
        $this->app->bind(BaseRepositoryInterface::class,UserProfileRepository::class);
        $this->app->bind(BaseRepositoryInterface::class,ClinicsRepository::class);
        $this->app->bind(BaseRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(BaseRepositoryInterface::class, ServiceRepository::class);
        $this->app->bind(BaseRepositoryInterface::class, BookingRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
