<?php

namespace App\Models;

use App\CoreLogic\Services\Availabilities\AnytimeAvailability;
use App\CoreLogic\Services\Availabilities\AvailabilityType;
use App\CoreLogic\Services\Availabilities\DynamicAvailability;
use App\CoreLogic\Services\Availabilities\FixedAvailability;
use App\CoreLogic\Services\Availabilities\FreeChoiceAvailability;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ProductAvailability extends Model
{
    use HasFactory;
    use HasUuids;
    use BelongsToTenant;

    public const DURATION_UNIT_TYPE_HOURS = 'hour';
    public const DURATION_UNIT_TYPE_MINUTES = 'minute';

    protected $guarded = [];

    protected $casts = [
        'available_days' => 'array',
        'all_day' => 'boolean',
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'start_time',
        'end_time'
    ];

    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @param Builder $query
     * @param string $productId
     * @return Builder
     */
    public function scopeForProduct(Builder $query, string $productId): Builder
    {
        return $query->where('product_id', $productId);
    }

    /**
     * @param Builder $query
     * @param Carbon $date
     * @return mixed
     */
    public function scopeForDate(Builder $query, Carbon $date)
    {
        return $this
            ->whereBetween('starts_at', [
                $date->tz('utc')->format('Y-m-d 00:00:00'),
                $date->tz('utc')->format('Y-m-d 23:59:59'),
            ]);
    }

    /**
     * @return HasMany
     */
    public function slots(): HasMany
    {
        return $this->hasMany(ProductAvailabilitySlot::class);
    }
}
