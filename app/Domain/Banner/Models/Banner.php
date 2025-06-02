<?php

namespace App\Domain\Banner\Models;

use App\Domain\Admin\Models\Admin;
use App\Domain\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Banner extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $guarded = [];

    const SHOW = 1;
    const HIDE = 0;
    const BANNER_MODE = [
        self::SHOW => 'Hiển thị',
        self::HIDE => 'Ẩn',
    ];

    const PART = [
        0 => 'Top',
        1 => 'Left',
        2 => 'Right',
        3 => 'Body Top 1',
        4 => 'Body Top 2',
        5 => 'Body Side Right 1',
        6 => 'Body Side Right 2',
        7 => 'Slider',
        8 => 'Footer',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->user_id = currentAdmin()->id;
        });
        static::saved(function ($model) {
            Cache::forget('banners');
        });
        static::deleted(function ($model) {
            Cache::forget('banners');
        });
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('banner')
            ->singleFile()
            ->useFallbackUrl('/backend/global_assets/images/placeholders/placeholder.jpg');
    }

    public function user()
    {
        return $this->belongsTo(Admin::class, 'user_id', 'id');
    }
}
