<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ProductLocation extends Model
{
    use HasFactory;
    use HasUuids;
    use BelongsToTenant;

    protected $fillable = [
        'product_id',
        'address_1',
        'address_2',
        'city',
        'state',
        'country',
        'postal_code',
        'lat',
        'long',
        'map_link',
        'address_type',
        'type',
        'tenant_id',
        'domain_id',
        'created_by',
    ];

    public const ADDRESS_TYPE = [
        'pickup' => 'pickup',
        'redeem_point' => 'redeem_point',
    ];
}
