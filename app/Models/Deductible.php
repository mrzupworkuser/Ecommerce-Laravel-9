<?php

namespace App\Models;

use App\CoreLogic\Enum\Deductible\DeductibleCategoryEnum;
use App\CoreLogic\Enum\Deductible\DeductibleTypeEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Deductible extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUuids;
    use BelongsToTenant;


    protected $casts = [
        'category' => DeductibleCategoryEnum::class,
        'type' => DeductibleTypeEnum::class,
        'is_price_inclusive' => 'boolean',
        'is_compounded' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'category',
        'type',
        'value',
        'is_price_inclusive',
        'is_compounded',
        'tenant_id',
        'domain_id',
        'created_by'
    ];
}
