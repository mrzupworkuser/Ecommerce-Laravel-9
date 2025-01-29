<?php

namespace App\TenancyFeatures;

use App\Models\Tenant;
use Aws\Result;
use Aws\S3\S3Client;
use Illuminate\Support\Arr;
use Stancl\Tenancy\Contracts\Feature;
use Stancl\Tenancy\Tenancy;

class AWSS3BucketFeature implements Feature
{
    protected S3Client $client;
    protected array $policies = [];

    public function __construct()
    {
        $this->client = $this->getClient();
    }

    public function bootstrap(Tenancy $tenancy): void
    {
        $tenancy->macro('createBucket', function (Tenant $tenant): void {
            $tenant->run(function ($tenant) {
                $this->createBucket($tenant);
            });
        });

        $tenancy->macro('deleteBucket', function (Tenant $tenant): void {
            $tenant->run(function ($tenant) {
                $this->delete($tenant);
            });
        });

        $tenancy->macro('bucketName', function (Tenant $tenant): void {
            $tenant->run(function ($tenant) {
                $this->getOrGenerateBucketName($tenant);
            });
        });
    }

    /**
     * @throws \JsonException
     */
    public function createBucket(Tenant $tenant): Result
    {
        $client = $this->getClient();
        $bucketName = $this->getOrGenerateBucketName($tenant);
        $bucketPolicy = $this->policies;

        $result = $client->createBucket([
            'Bucket' => $bucketName,
            'Policy' => json_encode($bucketPolicy, JSON_THROW_ON_ERROR),
            'ObjectLockEnabledForBucket' => true
        ]);

        $client->waitUntil('BucketExists', ['Bucket' => $bucketName]);

        $this->configureObjectHoldForBucket($bucketName);

        return $result;
    }

    public function getOrGenerateBucketName(Tenant $tenant): string
    {
        if (method_exists($tenant, 'getOrGenerateBucketName')) {
            return $tenant->getOrGenerateBucketName();
        }

        return $tenant->getKey();
    }

    protected function getClient(): S3Client
    {
        return new S3Client(
            $this->formatS3Config(
                config($this->getConfigName())
            )
        );
    }

    protected function formatS3Config(array $config): array
    {
        $config += ['version' => 'latest'];

        if (!empty($config['key']) && !empty($config['secret'])) {
            $config['credentials'] = Arr::only($config, ['key', 'secret', 'token']);
        }

        return $config;
    }

    public function getConfigName(): string
    {
        return 'filesystems.disks.tenant';
    }

    private function configureObjectHoldForBucket(string $bucketName): void
    {
        $this->client->putObjectLockConfiguration([
            'Bucket' => $bucketName,
            'ChecksumAlgorithm' => 'SHA256',
            'ObjectLockConfiguration' => [
                'ObjectLockEnabled' => 'Enabled',
                'Rule' => [
                    'DefaultRetention' => [
                        'Mode' => 'COMPLIANCE',
                        'Years' => 100,
                    ],
                ],
            ],
        ]);
    }

    public function delete($tenant): Result
    {
        return $this->client->deleteBucket([
            'Bucket' => $tenant->tenant_bucket
        ]);
    }
}
