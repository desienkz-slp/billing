<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:49:52              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Providers; use Illuminate\Support\ServiceProvider; use Illuminate\Support\Facades\Event; use App\Events\CustomerCreated; use App\Listeners\SyncCustomerToRouter; use App\Listeners\SyncCustomerToRadius; use App\Listeners\SendCustomerCreationNotification; class AppServiceProvider extends ServiceProvider { public function register(): void { } public function boot(): void { goto gZGLk; bQD5Z: Event::listen(CustomerCreated::class, SyncCustomerToRadius::class); goto JAZWG; gZGLk: \App\Models\Customer::observe(\App\Observers\CustomerObserver::class); goto CJxRC; JAZWG: Event::listen(CustomerCreated::class, SendCustomerCreationNotification::class); goto EnraH; CJxRC: Event::listen(CustomerCreated::class, SyncCustomerToRouter::class); goto bQD5Z; EnraH: } }
