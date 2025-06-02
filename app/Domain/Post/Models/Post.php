<?php

namespace App\Domain\Post\Models;

use App\Domain\Admin\Models\Admin;
use App\Domain\Menu\Models\MenuItem;
use App\Support\Traits\MenuItemTrait;
use App\Support\Traits\Taxonable;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Domain\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Post extends Model implements HasMedia
{
    use Sluggable;
    use Taxonable;
    use InteractsWithMedia;
    use MenuItemTrait;

    protected $casts = [
        'related_posts' => 'array',
        'on_pages' => 'array',
        'tags' => 'array'
    ];

    protected $guarded = [];

    /**
     * @inheritDoc
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('image')
            ->singleFile()
            ->useFallbackUrl('/backend/global_assets/images/placeholders/placeholder.jpg');
        $this
            ->addMediaCollection('file')
            ->singleFile()
            ->useFallbackUrl('/backend/global_assets/images/placeholders/placeholder.jpg');
    }

    public function url()
    {
        return route('post.show', $this->slug);
    }

    public function user()
    {
        return $this->belongsTo(Admin::class, 'user_id', 'id');
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
}
