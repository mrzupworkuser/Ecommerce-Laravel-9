<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ProductInventory extends Model
{
    use HasFactory;
    use BelongsToTenant;
    use HasUuids;

    public const INVENTORY_TYPE_DYNAMIC = 'dynamic';

    public const INVENTORY_TYPE_FIXED = 'fixed';

    public const INVENTORY_TYPE_UNLIMITED = 'unlimited';

    protected $fillable = [
        'product_id',
        'tenant_id',
        'created_by',
        'domain_id',
        'inventory_type',
        'quantity',
        'per_quantity_capacity'
    ];

    /**
     * @return int
     */
    public function totalCapacity(): int
    {
        return $this->quantity * $this->per_quantity_capacity;
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function assets()
    {
        return $this->belongsToMany(Asset::class, 'asset_product_inventory', 'product_inventory_id', 'asset_id');
    }
}
