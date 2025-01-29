<?php

namespace App\Models;

use App\Exceptions\DomainCannotBeChangedException;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Models\Domain as BaseDomain;

/**
 * @property int $id
 * @property int $tenant_id
 * @property string $domain
 * @property bool $is_primary
 * @property bool $is_fallback
 * @property string $certificate_status
 * @property Tenant $tenant
 */
class Domain extends BaseDomain
{
    use HasUuids;

    protected $casts = [
        'is_primary' => 'bool',
        'is_fallback' => 'bool',
        'data' => 'array'
    ];

    public static function booted()
    {
        static::updating(function (self $model) {
            if ($model->getAttribute('domain') !== $model->getOriginal('domain')) {
                throw new DomainCannotBeChangedException();
            }
        });

        static::saved(function (self $model) {
            $uniqueKeys = ['is_primary', 'is_fallback'];

            foreach ($uniqueKeys as $key) {
                if ($model->$key) {
                    $model->tenant->domains()
                        ->where('id', '!=', $model->id)
                        ->update([$key => false]);
                }
            }
        });
    }

    public static function domainFromSubdomain(string $subdomain): string
    {
        return $subdomain . '.' . config('tenancy.central_domains')[0];
    }

    public function makePrimary(): self
    {
        $this->update([
            'is_primary' => true,
        ]);

        $this->tenant->setRelation('primary_domain', $this);

        return $this;
    }

    public function makeFallback(): self
    {
        $this->update([
            'is_fallback' => true,
        ]);

        $this->tenant->setRelation('fallback_domain', $this);

        return $this;
    }

    public function isSubdomain(): bool
    {
        return ! Str::contains($this->domain, '.');
    }

    /**
     * @return string
     */
    public function getTypeAttribute(): string
    {
        return $this->isSubdomain() ? 'subdomain' : 'domain';
    }
}
