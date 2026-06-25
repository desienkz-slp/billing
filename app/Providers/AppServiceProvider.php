<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:30              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Providers; use Illuminate\Support\ServiceProvider; use Illuminate\Support\Facades\Event; use App\Events\CustomerCreated; use App\Listeners\SyncCustomerToRouter; use App\Listeners\SyncCustomerToRadius; use App\Listeners\SendCustomerCreationNotification; class AppServiceProvider extends ServiceProvider { public function register(): void { } public function boot(): void { goto nAPp8; DEedU: Event::listen(CustomerCreated::class, SyncCustomerToRouter::class); goto o5GQ_; teTWG: Event::listen(CustomerCreated::class, SendCustomerCreationNotification::class); goto h9UeA; nAPp8: \App\Models\Customer::observe(\App\Observers\CustomerObserver::class); goto DEedU; o5GQ_: Event::listen(CustomerCreated::class, SyncCustomerToRadius::class); goto teTWG; h9UeA: } }