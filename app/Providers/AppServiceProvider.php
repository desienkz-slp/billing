<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\CustomerCreated;
use App\Listeners\SyncCustomerToRouter;
use App\Listeners\SyncCustomerToRadius;
use App\Listeners\SendCustomerCreationNotification;

class AppServiceProvider extends ServiceProvider
{
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
        \App\Models\Customer::observe(\App\Observers\CustomerObserver::class);
        
        Event::listen(CustomerCreated::class, SyncCustomerToRouter::class);
        Event::listen(CustomerCreated::class, SyncCustomerToRadius::class);
        Event::listen(CustomerCreated::class, SendCustomerCreationNotification::class);
    }
}
