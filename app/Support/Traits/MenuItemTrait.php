<?php

namespace App\Support\Traits;

use App\Domain\Menu\Models\MenuItem;
use App\Domain\Taxonomy\Models\Taxon;

trait MenuItemTrait
{
    public function menu_items($type)
    {
        return $this->hasMany(MenuItem::class, 'item_id', 'id')->where('type', $type);
    }
}
