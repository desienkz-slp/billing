<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:36              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Providers; use Illuminate\Support\ServiceProvider; use Illuminate\Support\Facades\Event; use App\Events\CustomerCreated; use App\Listeners\SyncCustomerToRouter; use App\Listeners\SyncCustomerToRadius; use App\Listeners\SendCustomerCreationNotification; class AppServiceProvider extends ServiceProvider { public function register(): void { } public function boot(): void { goto EW1UL; DgwjL: Event::listen(CustomerCreated::class, SyncCustomerToRadius::class); goto FdhZk; EW1UL: \App\Models\Customer::observe(\App\Observers\CustomerObserver::class); goto P2tD0; P2tD0: Event::listen(CustomerCreated::class, SyncCustomerToRouter::class); goto DgwjL; FdhZk: Event::listen(CustomerCreated::class, SendCustomerCreationNotification::class); goto Y4OT2; Y4OT2: } }
