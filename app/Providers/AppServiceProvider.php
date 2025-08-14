<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AuthService;
use App\Services\EmailService;
use App\Services\ProductService;
use App\Services\MarqueeService;
use App\Services\AiService;
use App\Services\CartService;
use App\Services\HomeService;
use App\Services\ContactService;
use App\Repositories\UserRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Repositories\MarqueeRepository;
use App\Repositories\ContactRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Repositories
        $this->app->singleton(UserRepository::class);
        $this->app->singleton(OrderRepository::class);
        $this->app->singleton(ProductRepository::class);
        $this->app->singleton(MarqueeRepository::class);
        $this->app->singleton(ContactRepository::class);
        
        // Register Basic Services
        $this->app->singleton(EmailService::class);
        
        // Register Services with Dependencies
        $this->app->singleton(AuthService::class, function ($app) {
            return new AuthService(
                $app->make(EmailService::class),
                $app->make(UserRepository::class)
            );
        });

        $this->app->singleton(ProductService::class, function ($app) {
            return new ProductService(
                $app->make(ProductRepository::class)
            );
        });

        $this->app->singleton(MarqueeService::class, function ($app) {
            return new MarqueeService(
                $app->make(MarqueeRepository::class)
            );
        });

        $this->app->singleton(AiService::class, function ($app) {
            return new AiService(
                $app->make(ProductService::class)
            );
        });

        $this->app->singleton(CartService::class, function ($app) {
            return new CartService(
                $app->make(UserRepository::class),
                $app->make(ProductRepository::class),
                $app->make(OrderRepository::class)
            );
        });

        $this->app->singleton(HomeService::class, function ($app) {
            return new HomeService(
                $app->make(ProductService::class),
                $app->make(MarqueeService::class)
            );
        });

        $this->app->singleton(ContactService::class, function ($app) {
            return new ContactService(
                $app->make(ContactRepository::class)
            );
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
