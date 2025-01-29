<?php

namespace App\Models;

use App\Models\States\Offer\OfferStates;
use App\Models\States\Offer\OfferTypeStates;
use App\Models\Traits\HasArchivedStatus;
use App\CoreLogic\Enum\Offer\OfferTypeEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Offer extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUuids;
    use BelongsToTenant;
    use HasArchivedStatus;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_ARCHIVED = 'archived';
    public const DEFAULT_STATUS = self::STATUS_ACTIVE;

    protected $casts = [
        'settings' => 'array',
        'status' => OfferStates::class,
        'offerable.pivot.data' => 'array',
        'is_compounded' => 'boolean',
        'offer_type' => OfferTypeEnum::class,
    ];

    protected $fillable = [
        'tenant_id',
        'created_by',
        'title',
        'code',
        'code_type',
        'amount',
        'status',
        'start_at',
        'expired_at',
        'settings',
        'domain_id',
        'apply_once_per_type',
        'max_redemption_time',
        'products',
        'offer_type'
    ];

    public static function getCustomColumns(): array
    {
        return [
            'min_order_price',
            'max_order_price',
            'max_redeemable_amount',
            'include_tax_fee'
        ];
    }

    public function products()
    {
        return $this->morphedByMany(Product::class, 'offerable')->withPivot('id', 'data')->using(Offerable::class);
    }
}
