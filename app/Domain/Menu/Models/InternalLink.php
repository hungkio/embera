<?php

namespace App\Domain\Menu\Models;

use App\Support\Traits\IsSorted;
use App\Support\Traits\Taxonable;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Domain\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class InternalLink extends Model
{
    use Sluggable;
    use Taxonable;
    use InteractsWithMedia;
    use IsSorted;
    public $guarded = [];
    
    protected $fillable = ['url'];

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
}
