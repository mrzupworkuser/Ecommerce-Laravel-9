<?php

namespace App\Models;

use App\Models\States\ProductAvailabilitySlot\Available;
use App\Models\States\ProductAvailabilitySlot\ProductAvailabilitySlotState;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ProductAvailabilitySlot extends Model
{
    use HasFactory;
    use BelongsToTenant;
    use HasUuids;

    protected $fillable = [
        'product_availability_id',
        'product_id',
        'order_item_id',
        'assets',
        'start_at',
        'end_at',
        'status',
        'tenant_id',
        'domain_id',
        'max_quantity',
    ];

    protected $dates = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    protected $casts = [
        'assets' => 'array',
        'status' => ProductAvailabilitySlotState::class
    ];

    /**
     * @return BelongsTo
     */
    public function availability(): BelongsTo
    {
        return $this->belongsTo(ProductAvailability::class, 'product_availability_id');
    }

    /**
     * @return mixed
     */
    public function release()
    {
        return $this->status->release();
    }

    /**
     * @return null
     */
    public function hold()
    {
        if ($this->status->is(Available::class)) {
            return $this->status->hold();
        }

        return null;
    }
}
