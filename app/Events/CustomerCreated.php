<?php

namespace App\Events;

use App\Models\Customer;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $customer;
    public $shouldSyncPppoe;

    /**
     * Create a new event instance.
     *
     * @param Customer $customer
     * @param bool $shouldSyncPppoe
     */
    public function __construct(Customer $customer, bool $shouldSyncPppoe = false)
    {
        $this->customer = $customer;
        $this->shouldSyncPppoe = $shouldSyncPppoe;
    }
}
