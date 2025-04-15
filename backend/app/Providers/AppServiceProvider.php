<?php

namespace App\Providers;

use App\Interfaces\OrderRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\OrderRepository;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\Contracts\OrderServiceInterface;
use App\Services\OrderService;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserService::class, function($app) {
            return new UserService($app->make(UserRepositoryInterface::class));
        });

        $this->app->singleton(AuthService::class, function ($app) {
            return new AuthService();
        });

        $this->app->bind(OrderServiceInterface::class, OrderService::class);

        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(OrderService::class, function($app) {
            return new OrderService($app->make(OrderRepositoryInterface::class));
        });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
