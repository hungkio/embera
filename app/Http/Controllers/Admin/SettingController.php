<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Admin\Models\Admin;
use App\Domain\Menu\Models\Menu;
use App\Domain\Taxonomy\Models\Taxon;
use App\Domain\Taxonomy\Models\Taxonomy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingUpdateRequest;
use App\Support\ValuesStore\Setting;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    use AuthorizesRequests;

    public function edit()
    {
        $this->authorize('create', Admin::class);

        $taxonomies = Taxonomy::get();
        $popularCategories = Taxon::whereIn('id', setting('popular_categories', []))->with('ancestors')->get();
        $popularHomeCategories = Taxon::whereIn('id', setting('popular_home_categories', []))->with('ancestors')->get();
        $menus = Menu::where('status', Menu::STATUS_SHOW)->get();

        return view('admin.settings.edit', compact('popularCategories', 'taxonomies', 'popularHomeCategories', 'menus'));
    }

    public function update(SettingUpdateRequest $request)
    {
        $this->authorize('create', Admin::class);
        //Remove Cache Menu
        Cache::forget('menu-header');
        Cache::forget('menu-footer-1');
        Cache::forget('menu-footer-2');

        if ($request->hasFile('store_logo')){
            $storeLogo = Storage::putFile('store_logo', $request->file('store_logo'));
        }else{
            $storeLogo = setting('store_logo') ?? null;
        }

        if ($request->hasFile('store_favicon')){
            $storeFavicon = Storage::putFile('store_favicon', $request->file('store_favicon'));
        }else{
            $storeFavicon = setting('store_favicon') ?? null;
        }

        $settings = array_merge($request->validated(),
            [
                'store_banner' => $request->input('store_banner'),
                'mail_password' => $request->input('mail_password'),
                'post_taxonomy' => $request->input('post_taxonomy'),
                'store_logo' => $storeLogo,
                'store_favicon' => $storeFavicon,
                'menu_header' => $request->input('menu_header'),
                'menu_footer_1' => $request->input('menu_footer_1'),
                'menu_footer_2' => $request->input('menu_footer_2'),
                'banner' => $request->input('banner'),
                'language' => $request->input('language'),
                'custom_styles' => $request->input('custom_styles'),
                'custom_scripts' => $request->input('custom_scripts'),
                'store_taxon_level' => $request->input('store_taxon_level'),
                'store_menu_level' => $request->input('store_menu_level'),
                'analytics' => $request->input('analytics'),
            ]
        );

        foreach ($settings as $key => $value) {
            if (is_array($value)) {
                setting()->put($key, array_reset_index($value));
            } else {
                setting()->put($key, $value);
            }
        }

        logActivity(null, 'update');

        flash()->success(__('Cập nhật hệ thống thành công !'));

        return redirect()->back();
    }
}
