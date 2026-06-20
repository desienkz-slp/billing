<?php

namespace App\Observers;

use App\Models\Customer;
use App\Jobs\SyncCustomerToDbPusat;

class CustomerObserver
{
    public function created(Customer $customer)
    {
        SyncCustomerToDbPusat::dispatch($customer, 'create');
    }

    public function updated(Customer $customer)
    {
        SyncCustomerToDbPusat::dispatch($customer, 'update');
    }

    public function deleted(Customer $customer)
    {
        SyncCustomerToDbPusat::dispatch($customer, 'delete');
    }
}
