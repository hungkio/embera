<?php

namespace App\Composers;

use App\Domain\Menu\Models\MenuItem;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class MenuComposer
{
    /**
     * Bind data to view.
     */
    public function compose(View $view)
    {
        $menuHeaders = Cache::rememberForever('menu-header', function () {
            $rootMenu = MenuItem::where('menu_id', setting('menu_header', 1))->whereNull('parent_id')->first();
            if (!$rootMenu) {
                return collect([]);
            }

            return MenuItem::where('parent_id', $rootMenu->id)
                ->with('childs')->get();
        });
        $menuFooter1 = Cache::rememberForever('menu-footer-1', function () {
            $rootMenu = MenuItem::where('menu_id', setting('menu_footer_1', 1))->whereNull('parent_id')->first();
            if (!$rootMenu) {
                return collect([]);
            }

            return MenuItem::where('parent_id', $rootMenu->id)
                ->with('childs')->get();
        });
        $menuFooter2 = Cache::rememberForever('menu-footer-2', function () {
            $rootMenu = MenuItem::where('menu_id', setting('menu_footer_2', 1))->whereNull('parent_id')->first();
            if (!$rootMenu) {
                return collect([]);
            }

            return MenuItem::where('parent_id', $rootMenu->id)
                ->with('childs')->get();
        });
        $view->with('menuHeaders', $menuHeaders);
        $view->with('menuFooter1', $menuFooter1);
        $view->with('menuFooter2', $menuFooter2);
    }
}
