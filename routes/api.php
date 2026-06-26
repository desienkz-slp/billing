<?php

use App\Http\Controllers\Api\V1\AreaController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\PackageController;
use App\Http\Controllers\Api\V1\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — /api/v1/*
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Public API info
    Route::get('/', fn () => response()->json([
        'app' => 'LadaPala-Bill API',
        'version' => '1.0.0',
        'status' => 'ok',
    ]));

    // Auth (guest)
    Route::post('login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware(['auth:sanctum', 'tenant'])->group(function () {
        // Auth
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::get('dashboard', [\App\Http\Controllers\Api\V1\DashboardController::class, 'index']);

        // === CUSTOMERS ===
        Route::get('customers/dashboard-stats', [CustomerController::class, 'dashboardStats']);
        Route::get('customers/dashboard-search', [CustomerController::class, 'dashboardSearch']);
        Route::get('customers/dashboard-belum-bayar', [CustomerController::class, 'dashboardBelumBayar']);
        Route::get('customers/dashboard-telat-bayar', [CustomerController::class, 'dashboardTelatBayar']);
        Route::get('customers/dashboard-lunas-bulan-ini', [CustomerController::class, 'dashboardLunasBulanIni']);
        Route::get('customers/dashboard-transaksi-bulan-ini', [CustomerController::class, 'dashboardTransaksiBulanIni']);
        Route::get('customers/{customer}/payment-options', [CustomerController::class, 'paymentOptions'])
            ->middleware('permission:billing.payments.create');
            
        Route::middleware('permission:billing.customers.view')->group(function () {
            Route::get('customers', [CustomerController::class, 'index']);
            Route::get('customers/stats', [CustomerController::class, 'stats']);
            Route::get('customers/{customer}', [CustomerController::class, 'show']);
        });
        Route::post('customers', [CustomerController::class, 'store'])
            ->middleware('permission:billing.customers.create');
        Route::put('customers/{customer}', [CustomerController::class, 'update'])
            ->middleware('permission:billing.customers.edit');
        Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])
            ->middleware('permission:billing.customers.delete');

        // === PAYMENTS ===
        Route::middleware('permission:billing.payments.view')->group(function () {
            Route::get('payments', [PaymentController::class, 'index']);
            Route::get('payments/summary', [PaymentController::class, 'summary']);
            Route::get('payments/{payment}', [PaymentController::class, 'show']);
        });
        Route::post('payments', [PaymentController::class, 'store'])
            ->middleware('permission:billing.payments.create');
        Route::post('payments/{payment}/cancel', [PaymentController::class, 'cancel'])
            ->middleware('permission:billing.payments.cancel');

        // === MASTER DATA ===
        Route::apiResource('packages', PackageController::class)
            ->middleware('permission:master.packages.manage');
        Route::apiResource('areas', AreaController::class)
            ->middleware('permission:master.areas.manage');
            
        Route::apiResource('routers', \App\Http\Controllers\Api\V1\RouterController::class);
        Route::apiResource('odps', \App\Http\Controllers\Api\V1\OdpController::class);
        Route::apiResource('odcs', \App\Http\Controllers\Api\V1\OdcController::class);
        Route::apiResource('expenses', \App\Http\Controllers\Api\V1\ExpenseController::class);
        Route::apiResource('invoices', \App\Http\Controllers\Api\V1\InvoiceController::class);

        // === TRACKING & NOTIFICATIONS ===
        Route::post('tracking/update', [\App\Http\Controllers\Api\V1\TrackingController::class, 'updateLocation']);
        Route::get('tracking/live', [\App\Http\Controllers\Api\V1\TrackingController::class, 'liveLocations']);
        
        Route::post('auth/fcm-token', [AuthController::class, 'updateFcmToken']);
        
        // === PRINTER ===
        Route::get('payments/{payment}/print-data', [PaymentController::class, 'printData']);

        // Phase 3+ endpoints will be added here
    });
});
