<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-27 01:21:32              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Providers; use Illuminate\Support\ServiceProvider; use Illuminate\Support\Facades\Event; use App\Events\CustomerCreated; use App\Listeners\SyncCustomerToRouter; use App\Listeners\SyncCustomerToRadius; use App\Listeners\SendCustomerCreationNotification; class AppServiceProvider extends ServiceProvider { public function register(): void { } public function boot(): void { goto fodf3; tm_d_: Event::listen(CustomerCreated::class, SyncCustomerToRouter::class); goto jhs03; jhs03: Event::listen(CustomerCreated::class, SyncCustomerToRadius::class); goto jnXhc; jnXhc: Event::listen(CustomerCreated::class, SendCustomerCreationNotification::class); goto j0a5W; fodf3: \App\Models\Customer::observe(\App\Observers\CustomerObserver::class); goto tm_d_; j0a5W: } }
