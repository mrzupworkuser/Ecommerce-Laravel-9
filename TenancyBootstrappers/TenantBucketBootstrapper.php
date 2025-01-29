<?php

namespace App\TenancyBootstrappers;

use Illuminate\Contracts\Foundation\Application;
use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;

class TenantBucketBootstrapper implements TenancyBootstrapper
{
    protected string|null $originalBucket;

    public function __construct(
        protected Application $app
    ) {
        $this->originalBucket = $this->app['config']['filesystems.disks.s3.bucket'];
    }

    public function bootstrap(Tenant $tenant): void
    {
        $this->app['config']['filesystems.disks.s3.bucket'] = $tenant->bucket()->getOrGenerateBucketName($tenant);
        ;
    }

    public function revert(): void
    {
        $this->app['config']['filesystems.disks.s3.bucket'] = $this->originalBucket;
    }
}
