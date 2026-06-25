<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Web\StatisticController;
use App\Http\Controllers\Web\ExpenseController;

use App\Http\Controllers\Web\CustomerController;
use App\Http\Controllers\Web\CustomerDiagnosticController;
use App\Http\Controllers\Web\CutiController;
use App\Http\Controllers\Web\InvoiceController;
use App\Http\Controllers\Web\IsolirController;
use App\Http\Controllers\Web\PaymentApiController;
use App\Http\Controllers\Web\ReportController;
use App\Http\Controllers\Web\SettingsController;
use App\Http\Controllers\Web\ConfigController;
use App\Http\Controllers\Web\MapController;
use App\Http\Controllers\Web\MonitoringController;
use App\Http\Controllers\Web\RadiusController;
use App\Http\Controllers\Web\SuperAdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check() ? redirect(route('app-gateway', [], false)) : redirect(route('login', [], false)));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::middleware(['auth', 'tenant'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/app-gateway', [\App\Http\Controllers\GatewayController::class, 'index'])->name('app-gateway');
    Route::get('/cust/dashboard', [DashboardController::class, 'index'])->name('cust.dashboard');


    // Keuangan (Finance)
    Route::resource('reports/expenses', ExpenseController::class)->only(['index', 'store'])->middleware('permission:can_view_finance')->names('reports.expenses');
    Route::delete('reports/expenses/{expense}', [ExpenseController::class, 'destroy'])->middleware('permission:can_delete_finance')->name('reports.expenses.destroy');




    // 📦📦 Customers 📦📦📦📦📦📦📦📦📦📦📦📦📦📦📦📦📦📦📦📦📦📦📦📦📦📦📦📦📦📦📦📦
    Route::post('/cust/customers/batch-destroy', [CustomerController::class, 'batchDestroy'])
        ->middleware('permission:can_delete_customer')->name('cust.customers.batch-destroy');
    Route::get('/cust/customers/search-pppoe', [CustomerController::class, 'searchPppoeUsers'])
        ->name('cust.customers.search-pppoe');
    Route::get('/cust/customers/router-profiles/{router}', [CustomerController::class, 'getRouterProfiles'])
        ->name('cust.customers.router-profiles');
    Route::get('/cust/customers/radius-groups', [CustomerController::class, 'getRadiusGroups'])
        ->name('cust.customers.radius-groups');
        
    // Trashed Customers
    Route::get('/cust/customers/trashed', [CustomerController::class, 'trashed'])
        ->name('cust.customers.trashed');
    Route::post('/cust/customers/{customer}/restore', [CustomerController::class, 'restore'])
        ->name('cust.customers.restore')->withTrashed();
    Route::delete('/cust/customers/{customer}/force-delete', [CustomerController::class, 'forceDelete'])
        ->middleware('permission:can_delete_customer')->name('cust.customers.force-delete')->withTrashed();
    Route::post('/cust/customers/trashed/batch-restore', [CustomerController::class, 'batchRestore'])
        ->name('cust.customers.batch-restore');
    Route::post('/cust/customers/trashed/batch-force-delete', [CustomerController::class, 'batchForceDelete'])
        ->middleware('permission:can_delete_customer')->name('cust.customers.batch-force-delete');
        
    Route::get('/cust/customers', [CustomerController::class, 'index'])->name('cust.customers.index');
    Route::get('/cust/customers/create', [CustomerController::class, 'create'])
        ->middleware('permission:can_input_customer')->name('cust.customers.create');
    Route::post('/cust/customers', [CustomerController::class, 'store'])
        ->middleware('permission:can_input_customer')->name('cust.customers.store');
    Route::get('/cust/customers/{customer}/edit', [CustomerController::class, 'edit'])
        ->middleware('permission:can_edit_customer')->name('cust.customers.edit');
    Route::put('/cust/customers/{customer}', [CustomerController::class, 'update'])
        ->middleware('permission:can_edit_customer')->name('cust.customers.update');
    Route::delete('/cust/customers/{customer}', [CustomerController::class, 'destroy'])
        ->middleware('permission:can_delete_customer')->name('cust.customers.destroy');
    Route::get('/cust/customers/{customer}', [CustomerController::class, 'show'])
        ->name('cust.customers.show');

    // ── Customer Diagnostics ──────────────────────
    Route::get('/cust/customers-diagnostics/duplicates', [CustomerDiagnosticController::class, 'scanDuplicates'])
        ->name('cust.customers.diagnostics.duplicates');
    Route::get('/cust/customers-diagnostics/mismatch', [CustomerDiagnosticController::class, 'scanMismatch'])
        ->name('cust.customers.diagnostics.mismatch');
    Route::get('/cust/customers-diagnostics/unsynced', [CustomerDiagnosticController::class, 'scanUnsynced'])
        ->name('cust.customers.diagnostics.unsynced');

    // ── Pelanggan Cuti ────────────────────────────
    Route::get('/cust/cuti', [CutiController::class, 'index'])
        ->middleware('permission:can_manage_cuti')->name('cust.cuti.index');
    Route::post('/cust/cuti/batch-restore', [CutiController::class, 'batchRestore'])
        ->middleware('permission:can_manage_cuti')->name('cust.cuti.batch-restore');
    Route::post('/cust/cuti/batch-destroy', [CutiController::class, 'batchDestroy'])
        ->middleware('permission:can_delete_customer_cuti')->name('cust.cuti.batch-destroy');
    Route::post('/cust/cuti/{customer}/store', [CutiController::class, 'store'])
        ->middleware('permission:can_cuti')->name('cust.cuti.store');
    Route::post('/cust/cuti/{customer}/restore', [CutiController::class, 'restore'])
        ->middleware('permission:can_manage_cuti')->name('cust.cuti.restore');
    Route::delete('/cust/cuti/{customer}', [CutiController::class, 'destroy'])
        ->middleware('permission:can_delete_customer_cuti')->name('cust.cuti.destroy');

    // ── Payment API (AJAX) ────────────────────────
    Route::get('/api-web/payments/months/{customer}', [PaymentApiController::class, 'getMonths'])
        ->middleware('permission:can_process_payment')->name('api.payments.months');
    Route::post('/api-web/payments/single', [PaymentApiController::class, 'storeSingle'])
        ->middleware('permission:can_process_payment')->name('api.payments.single');
    Route::post('/api-web/payments/batch', [PaymentApiController::class, 'storeBatch'])
        ->middleware('permission:can_process_payment')->name('api.payments.batch');
    Route::get('/api-web/payments/wa-template/{customer}', [PaymentApiController::class, 'getWaTemplate'])
        ->middleware('permission:can_send_wa_invoice')->name('api.payments.wa-template');

    // ── Income / Pendapatan ────────────────────────────────────────────────────────────
    Route::get('/reports/income', [\App\Http\Controllers\Web\IncomeController::class, 'index'])->name('reports.income.index');
    Route::get('/reports/income/{payment}/print', [\App\Http\Controllers\Web\IncomeController::class, 'printInvoice'])->name('reports.income.print');
    Route::post('/reports/income/{payment}/resend', [\App\Http\Controllers\Web\IncomeController::class, 'resendInvoice'])->name('reports.income.resend');
    Route::post('/reports/income/{payment}/refund', [\App\Http\Controllers\Web\IncomeController::class, 'refund'])->middleware('permission:can_cancel_payment')->name('reports.income.refund');
    
    Route::get('/reports/other-incomes', [\App\Http\Controllers\Web\OtherIncomeController::class, 'index'])->name('reports.other-incomes.index');
    Route::post('/reports/other-incomes', [\App\Http\Controllers\Web\OtherIncomeController::class, 'store'])->name('reports.other-incomes.store');
    Route::delete('/reports/other-incomes/{otherIncome}', [\App\Http\Controllers\Web\OtherIncomeController::class, 'destroy'])->name('reports.other-incomes.destroy');

    // ── Management Isolir ────────────────────────────
    Route::prefix('cust')->name('cust.')->group(function () {
        Route::get('/isolir', [IsolirController::class, 'index'])
            ->middleware('permission:can_manage_isolir')->name('isolir.index');
        Route::post('/isolir/batch-isolate', [IsolirController::class, 'batchIsolate'])
            ->middleware('permission:can_manage_isolir')->name('isolir.batch-isolate');
        Route::post('/isolir/batch-release', [IsolirController::class, 'batchRelease'])
            ->middleware('permission:can_manage_isolir')->name('isolir.batch-release');
        Route::post('/isolir/{customer}/isolate', [IsolirController::class, 'isolate'])
            ->middleware('permission:can_manage_isolir')->name('isolir.isolate');
        Route::post('/isolir/{customer}/release', [IsolirController::class, 'release'])
            ->middleware('permission:can_manage_isolir')->name('isolir.release');
    });

    // ── Reports (Laporan) ─────────────────────────
    Route::get('/reports/statistics', [StatisticController::class, 'index'])
        ->middleware('permission:can_view_reports')->name('reports.statistics');
    Route::get('/reports/statistics/api', [StatisticController::class, 'apiData'])
        ->middleware('permission:can_view_reports')->name('reports.statistics.api');
    Route::post('/reports/statistics/toggle-hide', [StatisticController::class, 'toggleHideMonth'])
        ->middleware('permission:can_view_reports')->name('reports.statistics.toggle_hide');
    Route::get('/reports/finance', [ReportController::class, 'finance'])
        ->middleware('permission:can_view_finance')->name('reports.finance');
    Route::get('/reports/detail-pendapatan', [ReportController::class, 'detailPendapatan'])
        ->middleware('permission:can_view_reports')->name('reports.detail-pendapatan');
    Route::get('/reports/pengeluaran', [ReportController::class, 'pengeluaran'])
        ->middleware('permission:can_view_finance')->name('reports.pengeluaran');
    Route::post('/reports/pengeluaran', [ReportController::class, 'storePengeluaran'])
        ->middleware('permission:can_manage_expenses')->name('reports.pengeluaran.store');
    Route::get('/reports/tax', [ReportController::class, 'tax'])
        ->middleware('permission:can_view_finance')->name('reports.tax');
    Route::get('/reports/total', [ReportController::class, 'total'])
        ->middleware('permission:can_view_finance')->name('reports.total');
    Route::get('/reports/cashflow', [ReportController::class, 'cashflow'])
        ->middleware('permission:can_view_finance')->name('reports.cashflow');
    Route::get('/sales/fee', [ReportController::class, 'fee'])
        ->middleware('permission:can_view_finance')->name('sales.fee');
    Route::get('/sales/setoran', [ReportController::class, 'setoran'])
        ->middleware('permission:can_manage_deposits')->name('sales.setoran');
    Route::post('/sales/setoran', [ReportController::class, 'storeSetoran'])
        ->middleware('permission:can_manage_deposits')->name('sales.setoran.store');
    Route::delete('/sales/setoran/{deposit}', [ReportController::class, 'destroySetoran'])
        ->middleware('permission:can_manage_deposits')->name('sales.setoran.destroy');
    Route::get('/sales/mitra', [ReportController::class, 'mitra'])
        ->middleware('permission:can_view_finance')->name('sales.mitra');
    Route::get('/sales/mitra/{user}/deposits', [ReportController::class, 'mitraDeposits'])
        ->middleware('permission:can_view_finance')->name('sales.mitra.deposits');
    Route::get('/sales/saldo', [ReportController::class, 'saldo'])
        ->middleware('permission:can_view_finance')->name('sales.saldo');
    Route::post('/sales/saldo/{user}/add', [ReportController::class, 'addSaldo'])
        ->middleware('permission:can_manage_saldo')->name('sales.saldo.add');
    Route::post('/sales/saldo/{user}/deduct', [ReportController::class, 'deductSaldo'])
        ->middleware('permission:can_manage_saldo')->name('sales.saldo.deduct');
    Route::get('/sales/saldo/{user}/history', [ReportController::class, 'saldoHistory'])
        ->middleware('permission:can_view_finance')->name('sales.saldo.history');

    // ── Settings (Master Data) ────────────────────
    Route::get('/settings/users', [SettingsController::class, 'users'])
        ->middleware('permission:can_manage_users')->name('settings.users');
    Route::post('/settings/users', [SettingsController::class, 'storeUser'])
        ->middleware('permission:can_manage_users')->name('settings.users.store');
    Route::post('/settings/users/{targetUser}/toggle', [SettingsController::class, 'toggleUser'])
        ->middleware('permission:can_manage_users')->name('settings.users.toggle');
    Route::put('/settings/users/{targetUser}', [SettingsController::class, 'updateUser'])
        ->middleware('permission:can_manage_users')->name('settings.users.update');
    Route::post('/settings/users/{targetUser}/default-sales', [SettingsController::class, 'setDefaultSales'])
        ->middleware('permission:can_manage_users')->name('settings.users.default_sales');

    Route::get('/settings/packages', [SettingsController::class, 'packages'])
        ->middleware('permission:can_manage_packages')->name('settings.packages');
    Route::post('/settings/packages', [SettingsController::class, 'storePackage'])
        ->middleware('permission:can_manage_packages')->name('settings.packages.store');
    Route::put('/settings/packages/{package}', [SettingsController::class, 'updatePackage'])
        ->middleware('permission:can_manage_packages')->name('settings.packages.update');
    Route::delete('/settings/packages/{package}', [SettingsController::class, 'destroyPackage'])
        ->middleware('permission:can_manage_packages')->name('settings.packages.destroy');
    Route::get('/settings/packages/router/{router}/profiles', [SettingsController::class, 'routerProfiles'])
        ->middleware('permission:can_manage_packages')->name('settings.packages.router-profiles');

    Route::get('/settings/areas', [SettingsController::class, 'areas'])
        ->middleware('permission:can_manage_areas')->name('settings.areas');
    Route::post('/settings/areas', [SettingsController::class, 'storeArea'])
        ->middleware('permission:can_manage_areas')->name('settings.areas.store');

    Route::get('/settings/odps', [SettingsController::class, 'odps'])
        ->middleware('permission:can_manage_odp')->name('settings.odps');
    Route::post('/settings/odps', [SettingsController::class, 'storeOdp'])
        ->middleware('permission:can_manage_odp')->name('settings.odps.store');
    Route::delete('/settings/odps/{odp}', [SettingsController::class, 'destroyOdp'])
        ->middleware('permission:can_manage_odp')->name('settings.odps.destroy');

    Route::get('/settings/odcs', [SettingsController::class, 'odcs'])
        ->middleware('permission:can_manage_odp')->name('settings.odcs');
    Route::post('/settings/odcs', [SettingsController::class, 'storeOdc'])
        ->middleware('permission:can_manage_odp')->name('settings.odcs.store');
    Route::put('/settings/odcs/{odc}', [SettingsController::class, 'updateOdc'])
        ->middleware('permission:can_manage_odp')->name('settings.odcs.update');
    Route::delete('/settings/odcs/{odc}', [SettingsController::class, 'destroyOdc'])
        ->middleware('permission:can_manage_odp')->name('settings.odcs.destroy');

    Route::get('/settings/template-tagihan', [SettingsController::class, 'templateTagihan'])
        ->middleware('permission:can_edit_template')->name('settings.template-tagihan');
    Route::get('/settings/template-nota', [SettingsController::class, 'templateNota'])
        ->middleware('permission:can_edit_template')->name('settings.template-nota');

    // ── Config Module ─────────────────────────────
    Route::prefix('config')->middleware('permission:can_view_dashboard_config')->group(function () {
        Route::get('/', [ConfigController::class, 'index'])->name('config.index');
        // User Management (guarded by can_manage_users)
        Route::middleware('permission:can_manage_users')->group(function () {
            Route::get('/users', [ConfigController::class, 'users'])->name('config.users');
            Route::post('/users', [ConfigController::class, 'storeUser'])->name('config.users.store');
            Route::put('/users/{targetUser}', [ConfigController::class, 'updateUser'])->name('config.users.update');
            Route::post('/users/{targetUser}/toggle', [ConfigController::class, 'toggleUser'])->name('config.users.toggle');
            Route::post('/users/{targetUser}/default-sales', [ConfigController::class, 'setDefaultSales'])->name('config.users.default_sales');
        });

        // Role Management (guarded by can_manage_roles)
        Route::middleware('permission:can_manage_roles')->group(function () {
            Route::get('/roles', [ConfigController::class, 'roles'])->name('config.roles');
            Route::post('/roles', [ConfigController::class, 'storeRole'])->name('config.roles.store');
            Route::put('/roles/{role}', [ConfigController::class, 'updateRole'])->name('config.roles.update');
            Route::delete('/roles/{role}', [ConfigController::class, 'destroyRole'])->name('config.roles.destroy');
        });
        Route::get('/profil', [ConfigController::class, 'profil'])->name('config.profil');
        Route::post('/profil', [ConfigController::class, 'updateProfil'])->name('config.profil.update');
        Route::get('/template', [ConfigController::class, 'template'])->name('config.template');
        Route::post('/template', [ConfigController::class, 'updateTemplate'])->name('config.template.update');
        Route::get('/wa-gateway', [ConfigController::class, 'waGateway'])->name('config.wa-gateway');
        Route::get('/router', [ConfigController::class, 'router'])->name('config.router');
        Route::post('/router', [ConfigController::class, 'storeRouter'])->name('config.router.store');
        Route::put('/router/{router}', [ConfigController::class, 'updateRouter'])->name('config.router.update');
        Route::delete('/router/{router}', [ConfigController::class, 'destroyRouter'])->name('config.router.destroy');
        Route::post('/router/{router}/toggle', [ConfigController::class, 'toggleRouter'])->name('config.router.toggle');
        Route::post('/router/{router}/test', [ConfigController::class, 'testRouter'])->name('config.router.test');
        Route::get('/router/{router}/config', [ConfigController::class, 'getRouterConfig'])->name('config.router.config');
        Route::post('/router/{router}/backup', [ConfigController::class, 'backupRouter'])->name('config.router.backup');
        Route::get('/router/{router}/backups', [ConfigController::class, 'listRouterBackups'])->name('config.router.backups');
        Route::get('/router/{router}/restore/{backup}', [ConfigController::class, 'restoreRouterBackup'])->name('config.router.restore');
        Route::get('/router/{router}/download/{backup}', [ConfigController::class, 'downloadBackup'])->name('config.router.download');

        Route::get('/server', [ConfigController::class, 'server'])->name('config.server');
        Route::post('/server', [ConfigController::class, 'storeServer'])->name('config.server.store');
        Route::put('/server/{server}', [ConfigController::class, 'updateServer'])->name('config.server.update');
        Route::delete('/server/{server}', [ConfigController::class, 'destroyServer'])->name('config.server.destroy');
        Route::post('/server/{server}/toggle', [ConfigController::class, 'toggleServer'])->name('config.server.toggle');
        Route::post('/server/{server}/test', [ConfigController::class, 'testServer'])->name('config.server.test');
        Route::get('/server/acs-params', [ConfigController::class, 'getAcsParams'])->name('config.server.acs-params');
        Route::post('/server/acs-params', [ConfigController::class, 'saveAcsParams'])->name('config.server.acs-params.save');

        // RADIUS Server Management
        Route::middleware('permission:can_manage_radius')->group(function () {
            Route::get('/radius-server', [ConfigController::class, 'radiusServer'])->name('config.radius-server');
            Route::post('/radius-server', [ConfigController::class, 'storeRadiusServer'])->name('config.radius-server.store');
            Route::put('/radius-server/{server}', [ConfigController::class, 'updateRadiusServer'])->name('config.radius-server.update');
            Route::delete('/radius-server/{server}', [ConfigController::class, 'destroyRadiusServer'])->name('config.radius-server.destroy');
            Route::post('/radius-server/{server}/toggle', [ConfigController::class, 'toggleRadiusServer'])->name('config.radius-server.toggle');
            Route::post('/radius-server/{server}/test', [ConfigController::class, 'testRadiusServer'])->name('config.radius-server.test');
        });

        Route::get('/backup', [ConfigController::class, 'backup'])->middleware('permission:can_backup_restore')->name('config.backup');
        Route::get('/log-sistem', [ConfigController::class, 'logSistem'])->middleware('permission:can_view_audit_logs')->name('config.log-sistem');
        Route::get('/db-pusat', [ConfigController::class, 'dbPusat'])->name('config.db-pusat');
        Route::post('/db-pusat', [ConfigController::class, 'updateDbPusat'])->name('config.db-pusat.update');
    });

    // -------------------------------------------------------------
    // MODULE: MAP
    // -------------------------------------------------------------
    Route::prefix('map')->middleware('permission:can_view_dashboard_map')->group(function () {
        Route::get('/', [MapController::class, 'index'])->name('map.index');
        Route::post('/customer/{customer}/coordinate', [MapController::class, 'updateCoordinate'])
            ->middleware('permission:can_config_map')->name('map.coordinate.update');
        Route::delete('/customer/{customer}/coordinate', [MapController::class, 'deleteCoordinate'])
            ->middleware('permission:can_config_map')->name('map.coordinate.delete');
        Route::get('/api/customers', [MapController::class, 'apiCustomers'])->name('map.api.customers');
        Route::get('/api/odps', [MapController::class, 'apiOdps'])->name('map.api.odps');
        Route::get('/api/odcs', [MapController::class, 'apiOdcs'])->name('map.api.odcs');
    });

    // ── Monitoring Module ──────────────────
    Route::prefix('monitoring')->middleware('permission:can_view_monitor')->group(function () {
        Route::get('/', [MonitoringController::class, 'index'])->name('monitoring.index');
        Route::get('/router/{router}', [MonitoringController::class, 'routerDetail'])->name('monitoring.router.detail');
        Route::get('/router/{router}/active-json', [MonitoringController::class, 'activeConnections'])
            ->name('monitoring.active');
        Route::get('/router/{router}/resources-json', [MonitoringController::class, 'systemResources'])
            ->name('monitoring.resources');
        Route::get('/router/{router}/secrets-json', [MonitoringController::class, 'secretsJson'])
            ->name('monitoring.secrets.json');
        Route::post('/router/{router}/secrets/add', [MonitoringController::class, 'addSecret'])
            ->middleware('permission:can_manage_router')->name('monitoring.secrets.add');
        Route::post('/router/{router}/secrets/update-profile', [MonitoringController::class, 'updateProfile'])
            ->middleware('permission:can_manage_router')->name('monitoring.secrets.update-profile');
        Route::post('/router/{router}/secrets/delete', [MonitoringController::class, 'deleteSecret'])
            ->middleware('permission:can_manage_router')->name('monitoring.secrets.delete');
        Route::post('/router/{router}/sync', [MonitoringController::class, 'syncPppoe'])
            ->middleware('permission:can_manage_router')->name('monitoring.sync');
        Route::post('/router/{router}/isolir/{customer}', [MonitoringController::class, 'isolirCustomer'])
            ->middleware('permission:can_manage_router')->name('monitoring.isolir');
        Route::post('/router/{router}/unisolir/{customer}', [MonitoringController::class, 'unisolirCustomer'])
            ->middleware('permission:can_manage_router')->name('monitoring.unisolir');
    });

    // ── RADIUS Module ────────────────────────────
    Route::prefix('radius')->middleware('permission:can_view_radius')->group(function () {
        Route::get('/', [RadiusController::class, 'index'])->name('radius.index');
        Route::get('/server/{server}', [RadiusController::class, 'serverDetail'])->name('radius.server');
        
        // Proxy endpoints for Frontend to call via Axios
        Route::get('/api/server/{server}/config', [RadiusController::class, 'apiGetConfig'])->name('radius.api.config.get');
        Route::get('/api/server/{server}/sessions', [RadiusController::class, 'apiGetSessions'])->name('radius.api.sessions.get');
        Route::get('/api/server/{server}/users', [RadiusController::class, 'apiGetUsers'])->name('radius.api.users.get');
        Route::post('/api/server/{server}/users', [RadiusController::class, 'apiStoreUser'])->name('radius.api.users.store');
        Route::post('/api/server/{server}/users/batch-delete', [RadiusController::class, 'apiDestroyUsers'])->name('radius.api.users.batch-destroy');
        Route::post('/api/server/{server}/users/{username}/disable', [RadiusController::class, 'apiDisableUser'])->name('radius.api.users.disable');
        Route::post('/api/server/{server}/users/{username}/enable', [RadiusController::class, 'apiEnableUser'])->name('radius.api.users.enable');

        Route::get('/api/server/{server}/profiles', [RadiusController::class, 'apiGetProfiles'])->name('radius.api.profiles.get');
        Route::post('/api/server/{server}/profiles', [RadiusController::class, 'apiStoreProfile'])->name('radius.api.profiles.store');
        Route::put('/api/server/{server}/profiles/{id}', [RadiusController::class, 'apiUpdateProfile'])->name('radius.api.profiles.update');
        Route::delete('/api/server/{server}/profiles/{id}', [RadiusController::class, 'apiDestroyProfile'])->name('radius.api.profiles.destroy');
    });

    // ── ACS Module ───────────────────────────────
    Route::prefix('acs')->middleware('permission:can_view_acs')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\AcsController::class, 'index'])->name('acs.index');
    });

    // ── OLT Module ───────────────────────────────
    Route::prefix('olt')->middleware('permission:can_view_dashboard_olt')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\OltController::class, 'index'])->name('olt.index');
    });
});

// ==================== SUPER ADMIN (Cross-Tenant) ====================
Route::prefix('superadmin')
    ->middleware(['auth', 'system_admin'])
    ->group(function () {
        Route::get('/', [SuperAdminController::class, 'index'])->name('superadmin.index');

        // Tenant CRUD
        Route::get('/tenants', [SuperAdminController::class, 'tenants'])->name('superadmin.tenants');
        Route::post('/tenants', [SuperAdminController::class, 'storeTenant'])->name('superadmin.tenants.store');
        Route::put('/tenants/{tenant}', [SuperAdminController::class, 'updateTenant'])->name('superadmin.tenants.update');
        Route::post('/tenants/{tenant}/toggle', [SuperAdminController::class, 'toggleTenant'])->name('superadmin.tenants.toggle');
        Route::delete('/tenants/{tenant}', [SuperAdminController::class, 'destroyTenant'])->name('superadmin.tenants.destroy');

        // Subscription
        Route::get('/subscriptions', [SuperAdminController::class, 'subscriptions'])->name('superadmin.subscriptions');
        Route::get('/saas-packages', [SuperAdminController::class, 'saasPackages'])->name('superadmin.packages');
        Route::post('/subscriptions', [SuperAdminController::class, 'storeSubscription'])->name('superadmin.subscriptions.store');
        Route::put('/subscriptions/{subscription}', [SuperAdminController::class, 'updateSubscription'])->name('superadmin.subscriptions.update');

        // Tenant detail
        Route::get('/tenants/{tenant}/detail', [SuperAdminController::class, 'tenantDetail'])->name('superadmin.tenants.detail');

        // Profile
        Route::get('/profile', [SuperAdminController::class, 'profile'])->name('superadmin.profile');
        Route::put('/profile', [SuperAdminController::class, 'updateProfile'])->name('superadmin.profile.update');
    });

