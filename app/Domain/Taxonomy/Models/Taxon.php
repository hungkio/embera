<?php

declare(strict_types=1);

namespace App\Domain\Taxonomy\Models;

use App\Domain\Model;
use App\Support\Traits\IsSorted;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Cache;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

/**
 * App\Domain\Taxonomy\Models\Taxon.
 *
 * @property int $id
 * @property int|null $parent_id
 * @property int $taxonomy_id
 * @property string $slug
 * @property string $name
 * @property string|null $description
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property int $order_column
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\Taxonomy\Models\Taxon[] $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\Media\Models\Media[] $media
 * @property-read int|null $media_count
 * @property-read \App\Domain\Taxonomy\Models\Taxon|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\Product\Models\Product[] $products
 * @property-read int|null $products_count
 * @property-read \App\Domain\Taxonomy\Models\Taxonomy $taxonomy
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxon breadthFirst()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxon depthFirst()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxon findSimilarSlugs($attribute, $config, $slug)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxon hasChildren()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxon hasParent()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxon isLeaf()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxon isRoot()
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxon newModelQuery()
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxon newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxon ordered($direction = 'asc')
 * @method static \Staudenmeir\LaravelAdjacencyList\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxon query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxon tree()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxon whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxon whereDepth($operator, $value = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxon whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxon whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxon whereMetaKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxon whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxon whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxon whereOrderColumn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxon whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxon whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxon whereTaxonomyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxon whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxon withRelationshipExpression($direction, $constraint, $initialDepth, $from = null)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Taxonomy\Models\Taxon treeOf($constraint, $maxDepth = null)
 */
class Taxon extends Model implements HasMedia
{
    use IsSorted;
    use InteractsWithMedia;
    use HasRecursiveRelationships;
    use Sluggable;

    protected $table = 'taxons';

    protected static function boot()
    {
        parent::boot();
    }

    public static function forSelect()
    {
        return static::select('id', 'name')
            ->get();
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('icon')
            ->singleFile()
            ->useFallbackUrl('/backend/global_assets/images/placeholders/placeholder.jpg');

        $this
            ->addMediaCollection('logo')
            ->singleFile()
            ->useFallbackUrl('');

        $this
            ->addMediaCollection('banner')
            ->singleFile()
            ->useFallbackUrl('');
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    public function selectText(): string
    {
        $prettyName = '';
        if ($this->ancestors->isNotEmpty()) {
            foreach ($this->ancestors as $ancestor) {
                $prettyName .= $ancestor->name.' -> ';
            }
        }
        $prettyName .= $this->name;

        return $prettyName;
    }

    public function urlPost()
    {
        return route('post.index')."?category=$this->slug";
    }

    public function childs(){
        return $this->hasMany(Taxon::class, 'parent_id', 'id');
    }

    public function parents(){
        return $this->belongsTo(Taxon::class, 'parent_id', 'id');
    }
}
