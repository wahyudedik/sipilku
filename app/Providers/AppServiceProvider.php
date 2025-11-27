<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\Service;
use App\Models\StoreProduct;
use App\Models\FactoryProduct;
use App\Observers\StoreProductObserver;
use App\Observers\FactoryProductObserver;
use App\Policies\ProductPolicy;
use App\Policies\ServicePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Product::class => ProductPolicy::class,
        Service::class => ServicePolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observers for price history tracking
        StoreProduct::observe(StoreProductObserver::class);
        FactoryProduct::observe(FactoryProductObserver::class);
    }
}
