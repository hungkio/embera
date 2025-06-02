<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Menu\Models\Menu;
use App\Domain\Menu\Models\MenuItem;
use App\Http\Requests\Admin\AdminMenuItemRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuItemController
{
    public function edit(MenuItem $menuItem)
    {
        $menuItem->load( 'media');
        return view('admin.catalogs.taxons.edit', compact('menuItem'));
    }

    public function update(AdminMenuItemRequest $request)
    {
        $menuItem = MenuItem::findOrFail($request->menu_item_id);
        DB::transaction(function () use ($request, $menuItem){
            $menuItem->name = $request->name;
            $menuItem->type = $request->type;
            $menuItem->item_content = $request->item_content;
            $menuItem->item_id = $request->item_id;
            $menuItem->save();
        });
        logActivity($menuItem, 'update');

        flash()->success(__('Menu ":model" đã được cập nhật thành công !', ['model' => $menuItem->name]));

        return redirect()->back();
    }

    public function store(AdminMenuItemRequest $request)
    {
        $menu_item = MenuItem::create([
            'name' => $request->name,
            'menu_id' => 1,
            'type' => $request->type,
            'item_id' => $request->item_id,
            'item_content' => $request->item_content,
            'status' => MenuItem::STATUS_SHOW,
            'parent_id' => $request->parent_id,
        ]);

        logActivity($menu_item, 'create');

        flash()->success(__('Menu ":model" đã được thêm thành công !', ['model' => $menu_item->name]));

        return response()->json([
            'id' => $menu_item->id,
        ]);
    }

    public function destroy(MenuItem $menuItem): JsonResponse
    {
        $menuItem_name = $menuItem->name;
        DB::transaction(function () use ($menuItem){
            logActivity($menuItem, 'delete');
            $menuItem->childs()->delete();
            $menuItem->delete();
        });

        return response()->json([
            'status' => true,
            'message' => __('Menu ":model" đã xóa thành công !', ['model' => $menuItem_name])
        ]);
    }

    public function tree(Menu $menu, MenuItem $menuItem)
    {
        $tree = $menuItem->children()
            ->withCount('children')
            ->ordered()
            ->get()
            ->map(function ($child) {
                return ['id' => $child->id, 'text' => $child->name, 'children' => $child->children_count > 0];
            });
        return response()->json($tree);
    }

}
