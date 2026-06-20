<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\MonthlyBalance;
use App\Models\Payment;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class DummyCustomerSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::where('slug', 'demo')->first() ?? Tenant::first();
        if (!$tenant) return;
        
        app()->instance('current_tenant', $tenant);

        // Remove old dummy customers to avoid clutter
        Customer::where('tenant_id', $tenant->id)->delete();
        MonthlyBalance::where('tenant_id', $tenant->id)->delete();
        Payment::where('tenant_id', $tenant->id)->delete();
        \App\Models\OtherIncome::where('tenant_id', $tenant->id)->delete();
        \App\Models\Expense::where('tenant_id', $tenant->id)->delete();

        $admin = User::where('username', 'admin')->first() ?? User::first();
        $currentPeriod = now()->format('Y-m');
        $lastPeriod = now()->subMonth()->format('Y-m');
        $twoMonthsAgo = now()->subMonths(2)->format('Y-m');
        $today = now()->day;

        $package = \App\Models\Package::where('tenant_id', $tenant->id)->first();
        $area = \App\Models\Area::where('tenant_id', $tenant->id)->first();

        // Helper to create a customer
        $createCustomer = function($name, $regDate, $billingDate, $phone, $overrides = []) use ($tenant, $package, $area) {
            return Customer::create(array_merge([
                'tenant_id' => $tenant->id,
                'name' => $name,
                'phone' => $phone,
                'address' => 'Jl. Dummy No. ' . rand(1, 100),
                'registration_date' => $regDate,
                'billing_date' => $billingDate,
                'package_id' => $package ? $package->id : null,
                'area_id' => $area ? $area->id : null,
                'status' => 'active'
            ], $overrides));
        };

        // 1. Baru (Lunas) - 3 Customers
        for ($i=1; $i<=3; $i++) {
            $c = $createCustomer("Pelanggan Baru $i", now(), $today < 28 ? $today + 5 : 5, "081200100$i");
            MonthlyBalance::create([
                'tenant_id' => $tenant->id, 'customer_id' => $c->id,
                'period' => $currentPeriod, 'charge_amount' => 150000, 'balance' => 0, 'status' => 'paid'
            ]);
        }

        // 2. Lunas (Lama) - 3 Customers
        for ($i=1; $i<=3; $i++) {
            $c = $createCustomer("Pelanggan Lunas $i", now()->subMonths(3), $today < 28 ? $today + 5 : 5, "081200200$i");
            MonthlyBalance::create([
                'tenant_id' => $tenant->id, 'customer_id' => $c->id,
                'period' => $lastPeriod, 'charge_amount' => 150000, 'balance' => 0, 'status' => 'paid'
            ]);
            MonthlyBalance::create([
                'tenant_id' => $tenant->id, 'customer_id' => $c->id,
                'period' => $currentPeriod, 'charge_amount' => 150000, 'balance' => 0, 'status' => 'paid'
            ]);
        }

        // 3. Jatuh Tempo - 3 Customers (Billing date in the past)
        for ($i=1; $i<=3; $i++) {
            $billDate = $today > 1 ? $today - 1 : 1; 
            $c = $createCustomer("Pelanggan Jatuh Tempo $i", now()->subMonths(2), $billDate, "081200300$i");
            MonthlyBalance::create([
                'tenant_id' => $tenant->id, 'customer_id' => $c->id,
                'period' => $currentPeriod, 'charge_amount' => 150000, 'balance' => 150000, 'status' => 'unpaid'
            ]);
        }

        // 4. Telat - 3 Customers (Unpaid last month)
        for ($i=1; $i<=3; $i++) {
            $c = $createCustomer("Pelanggan Telat $i", now()->subMonths(3), $today, "081200400$i");
            MonthlyBalance::create([
                'tenant_id' => $tenant->id, 'customer_id' => $c->id,
                'period' => $lastPeriod, 'charge_amount' => 150000, 'balance' => 150000, 'status' => 'unpaid'
            ]);
            MonthlyBalance::create([
                'tenant_id' => $tenant->id, 'customer_id' => $c->id,
                'period' => $currentPeriod, 'charge_amount' => 150000, 'balance' => 150000, 'status' => 'unpaid'
            ]);
        }

        // 5. Cuti - 3 Customers
        for ($i=1; $i<=3; $i++) {
            $c = $createCustomer("Pelanggan Cuti $i", now()->subMonths(5), 10, "081200500$i", [
                'status' => 'cuti',
                'is_on_leave' => true,
                'leave_start' => now()->startOfMonth(),
            ]);
        }

        // 6. Isolir - 3 Customers
        for ($i=1; $i<=3; $i++) {
            $c = $createCustomer("Pelanggan Isolir $i", now()->subMonths(4), 10, "081200600$i", [
                'status' => 'isolated',
                'is_isolated' => true,
                'isolated_since' => now()->subDays(5),
            ]);
            MonthlyBalance::create([
                'tenant_id' => $tenant->id, 'customer_id' => $c->id,
                'period' => $lastPeriod, 'charge_amount' => 150000, 'balance' => 150000, 'status' => 'unpaid'
            ]);
        }

        // 7. Generate Payments for this month (Income & Tax & Fee)
        $firstCustomer = Customer::where('tenant_id', $tenant->id)->where('status', 'active')->first();
        if ($admin && $firstCustomer) {
            for ($i=1; $i<=3; $i++) {
                Payment::create([
                    'tenant_id' => $tenant->id,
                    'customer_id' => $firstCustomer->id,
                    'amount' => 150000,
                    'paid_amount' => 150000,
                    'period' => $currentPeriod,
                    'ppn_amount' => 15000, // Tax
                    'collector_fee_amount' => 5000,
                    'sales_fee_amount' => 5000,
                    'payment_date' => now()->subDays(rand(1, 5)),
                    'status' => 'paid',
                    'payment_method' => 'cash',
                    'collected_by' => $admin->id,
                ]);
            }
            
            // Last month payment
            Payment::create([
                'tenant_id' => $tenant->id,
                'customer_id' => $firstCustomer->id,
                'amount' => 150000,
                'paid_amount' => 150000,
                'period' => $lastPeriod,
                'ppn_amount' => 15000, // Tax
                'collector_fee_amount' => 5000,
                'sales_fee_amount' => 5000,
                'payment_date' => now()->subMonth()->startOfMonth()->addDays(5),
                'status' => 'paid',
                'payment_method' => 'transfer',
                'collected_by' => $admin->id,
            ]);
        }

        // 8. Generate Other Incomes
        \App\Models\OtherIncome::create([
            'tenant_id' => $tenant->id,
            'amount' => 200000,
            'category' => 'Biaya Instalasi Baru',
            'notes' => 'Pasang baru ruko ujung',
            'income_date' => now()->subDays(2),
            'created_by' => $admin ? $admin->id : null,
        ]);
        \App\Models\OtherIncome::create([
            'tenant_id' => $tenant->id,
            'amount' => 50000,
            'category' => 'Jual Router Bekas',
            'notes' => 'Router ZTE F609',
            'income_date' => now()->subMonth()->addDays(10),
            'created_by' => $admin ? $admin->id : null,
        ]);

        // 9. Generate Expenses
        \App\Models\Expense::create([
            'tenant_id' => $tenant->id,
            'amount' => 350000,
            'category' => 'Beli Kabel Fiber',
            'description' => 'Kabel drop core 1 roll',
            'expense_date' => now()->subDays(1),
            'status' => 'active',
            'created_by' => $admin ? $admin->id : null,
        ]);
        \App\Models\Expense::create([
            'tenant_id' => $tenant->id,
            'amount' => 50000,
            'category' => 'Konsumsi Teknisi',
            'description' => 'Makan siang tarikan kabel baru',
            'expense_date' => now()->subMonth()->addDays(12),
            'status' => 'active',
            'created_by' => $admin ? $admin->id : null,
        ]);
    }
}
