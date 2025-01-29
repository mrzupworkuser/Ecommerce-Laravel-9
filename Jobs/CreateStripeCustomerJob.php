<?php

namespace App\Jobs;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateStripeCustomerJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Tenant $tenant
    ) {
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        if (app()->environment('testing')) {
            return;
        }

        $this->tenant->run(function ($tenant) {
            $tenant->createAsStripeCustomer([
                'name'  => $this->tenant->company,
                'email' => $this->tenant->email,
                'phone' => $this->tenant->phone ?? '',
                'metadata' => [
                    'tenant_id' => $this->tenant->getKey(),
                    'subdomain' => $this->tenant->domain,
                ]
            ]);
        });
    }
}
