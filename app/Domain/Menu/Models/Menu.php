<?php

namespace App\Domain\Menu\Models;

use App\Support\Traits\IsSorted;
use App\Support\Traits\Taxonable;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Domain\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Menu extends Model implements HasMedia
{
    use Sluggable;
    use Taxonable;
    use InteractsWithMedia;
    use IsSorted;

    protected $guarded = [];
    protected $table = 'menus';
    protected $fillable = ['name', 'status', 'internal_url', 'external_link', 'order_column', 'position', 'lang'];

    const STATUS_HIDE = 0;
    const STATUS_SHOW = 1;
    const STATUS = [
        self::STATUS_HIDE => 'Ẩn',
        self::STATUS_SHOW => 'Hiển thị',
    ];

    const POSITION = [
        'topbar' => 'Top Bar',
        'main' => 'Main',
        'hot' => 'Hot',
        'partner' => 'Partner link',
        'quick-link-1' => 'Quick Link 1',
        'quick-link-2' => 'Quick Link 2',
        'quick-link-3' => 'Quick Link 3',
        'quick-link-4' => 'Quick Link 4',
        'quick-link-5' => 'Quick Link 5',
        'quick-link-6' => 'Quick Link 6',
        'footer' => 'Footer ',
        'right-cup' => 'Ds Cup cột phải',
        'right-result' => 'Kết quả bóng đá cột phải'
    ];
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

    public function menus()
    {
        return $this->hasMany(MenuItem::class, 'menu_id', 'id');
    }

    public function rootMenuItem()
    {
        return $this->hasOne(MenuItem::class, 'menu_id', 'id')->whereNull('parent_id');
    }
}
