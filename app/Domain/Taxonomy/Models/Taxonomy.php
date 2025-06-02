<?php

declare(strict_types=1);

namespace App\Domain\Taxonomy\Models;

use App\Domain\Model;
use App\Support\Traits\IsSorted;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Domain\Taxonomy\Models\Taxonomy.
 *
 * @property int $id
 * @property int $order_column
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Domain\Taxonomy\Models\Taxon|null $rootTaxon
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\Taxonomy\Models\Taxon[] $taxons
 * @property-read int|null $taxons_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxonomy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxonomy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxonomy ordered($direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxonomy query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxonomy whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxonomy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxonomy whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxonomy whereOrderColumn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxonomy whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Taxonomy extends Model
{
    use IsSorted;

    public function taxons(): HasMany
    {
        return $this->hasMany(Taxon::class, );
    }

    public function rootTaxon(): HasOne
    {
        return $this->hasOne(Taxon::class, )->whereNull('parent_id');
    }
}
