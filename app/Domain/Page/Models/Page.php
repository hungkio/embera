<?php

declare(strict_types=1);

namespace App\Domain\Page\Models;

use App\Domain\Admin\Models\Admin;
use App\Domain\Model;
use App\Support\Traits\MenuItemTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Cache;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * App\Domain\Page\Models\Page.
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $body
 * @property int $created_by
 * @property string $group
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Page\Models\Page findSimilarSlugs($attribute, $config, $slug)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Page\Models\Page newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Page\Models\Page newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Page\Models\Page query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Page\Models\Page whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Page\Models\Page whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Page\Models\Page whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Page\Models\Page whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Page\Models\Page whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Page\Models\Page whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Page\Models\Page whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Page\Models\Page whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Page\Models\Page whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Page extends Model implements HasMedia
{
    use Sluggable;
    use InteractsWithMedia;
    use MenuItemTrait;

    public static function boot()
    {
        parent::boot();
//        static::creating(function ($model) {
//            $model->user_id = currentAdmin()->id;
//        });
        static::saved(function ($model) {
            Cache::forget('pages');
        });
        static::deleted(function ($model) {
            Cache::forget('pages');
        });
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
            ],
        ];
    }

    public function url()
    {
        return route('page.show', $this->slug);
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('image')
            ->singleFile()
            ->useFallbackUrl('/backend/global_assets/images/placeholders/placeholder.jpg');
    }

    public function user()
    {
        return $this->belongsTo(Admin::class, 'user_id', 'id');
    }
}
