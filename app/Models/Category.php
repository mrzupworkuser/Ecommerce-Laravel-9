<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Category extends Model
{
    use HasFactory;
    use HasUuids;
    use BelongsToTenant;
    use SoftDeletes;

    public static array $TYPES = [
        'product',
        'asset'
    ];

    protected $fillable = [
        'name', 'description', 'tenant_id', 'domain_id', 'created_by', 'type'
    ];

    /**
     * @return BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return MorphTo
     */
    public function categorizables(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return MorphToMany
     */
    public function assets(): MorphToMany
    {
        return $this->morphedByMany(Asset::class, 'categorizable');
    }

    /**
     * @return MorphToMany
     */
    public function products(): MorphToMany
    {
        return $this->morphedByMany(Product::class, 'categorizable');
    }
}
