<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ProductPricing extends Model
{
    use HasFactory;
    use HasUuids;
    use BelongsToTenant;
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'unit_id',
        'tenant_id',
        'domain_id',
        'pricing_structure_type',
        'min_quantity',
        'max_quantity',
        'price',
        'price_type',
    ];

    public const PRICING_STRUCTURE_TYPES = [
        'byPerson' => 'By Person',
        'fixed' => 'Fixed',
        'perMinute' => 'Per Minute',
        'perHour' => 'Per Hour',
        'perDay' => 'Per Day',
        'perItem' => 'Per Item',
    ];

    /**
     * @return BelongsTo
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * @return BelongsTo
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
