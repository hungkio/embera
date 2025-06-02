<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Menu\Models\MenuItem;
use App\Domain\Taxonomy\Models\Taxon;
use App\Http\Requests\Admin\AdminBulkDeleteRequest;
use App\Http\Requests\Admin\TaxonomyRequest;
use App\DataTables\TaxonomyDataTable;
use App\Domain\Taxonomy\Actions\TaxonomyCreateAction;
use App\Domain\Taxonomy\Actions\TaxonomyDeleteAction;
use App\Domain\Taxonomy\Models\Taxonomy;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TaxonomyController
{
    use AuthorizesRequests;

    public function index(TaxonomyDataTable $dataTable)
    {
        $this->authorize('view', Taxonomy::class);

        return $dataTable->render('admin.catalogs.taxonomies.index');
    }

    public function create(): View
    {
        $this->authorize('create', Taxonomy::class);

        return view('admin.catalogs.taxonomies.create');
    }

    public function store(TaxonomyRequest $request, TaxonomyCreateAction $action): RedirectResponse
    {
        $this->authorize('create', Taxonomy::class);

        $taxonomy = $action->execute($request->validated());

        flash()->success(__('":model" đã tạo thành công !', ['model' => $taxonomy->name]));

        logActivity($taxonomy, 'create'); // log activity

        return redirect()->route('admin.taxonomies.edit', $taxonomy->id);
    }

    public function edit(Taxonomy $taxonomy): View
    {
        $this->authorize('update', $taxonomy);

        return view('admin.catalogs.taxonomies.edit', compact('taxonomy'));
    }

    public function update(TaxonomyRequest $request, Taxonomy $taxonomy): RedirectResponse
    {
        $this->authorize('update', $taxonomy);

        $taxonomy->update($request->validated());

        Taxon::whereTaxonomyId($taxonomy->id)->where('parent_id', null)->update(['name' => $request->name]);

        flash()->success(__('":model" đã cập nhật thành công !', ['model' => $taxonomy->name]));

        logActivity($taxonomy, 'update'); // log activity

        return intended($request, route('admin.taxonomies.edit', $taxonomy));
    }

    public function destroy(Taxonomy $taxonomy, TaxonomyDeleteAction $action): JsonResponse
    {
        $this->authorize('delete', $taxonomy);
        $taxonables = \DB::table('taxonables')->where('taxon_id', $taxonomy->rootTaxon->id ?? 0)->get();
        $menus = MenuItem::where('type', MenuItem::TYPE_CATEGORY)->where('item_id', $taxonomy->rootTaxon->id ?? 0)->get();

        if (setting('post_taxonomy', 0) == $taxonomy->id || !$taxonables->isEmpty() || !$menus->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => __('Danh mục đang được sử dụng. Không thể xóa!')
            ]);
        }

        logActivity($taxonomy, 'delete'); // log activity

        $action->execute($taxonomy);

        return response()->json([
            'status' => true,
            'message' => __('":model" đã xóa thành công !', ['model' => $taxonomy->name])
        ]);
    }


    public function bulkDelete(AdminBulkDeleteRequest $request): JsonResponse
    {
        $this->authorize('delete', Admin::class);

        $ids = [];
        foreach ($request->input('id') as $id) {
            $taxon = Taxon::where('taxonomy_id', $id)->first();
            $taxonables = \DB::table('taxonables')->where('taxon_id', $taxon->id ?? 0)->get();
            $menus = MenuItem::where('type', MenuItem::TYPE_CATEGORY)->where('item_id', $taxon->id ?? 0)->get();
            if (setting('post_taxonomy', 0) != $id && $taxonables->isEmpty() && $menus->isEmpty()) {
                $ids[] = $id;
            }
        }
        $deletedRecord = Taxonomy::whereIn('id', $ids)->delete();

        return response()->json([
            'status' => true,
            'message' => __('Đã xóa thành công ":count" loại danh mục và ":count_fail" danh mục đang được sử dụng, không thể xoá!',
                [
                    'count' => $deletedRecord,
                    'count_fail' => count($request->input('id')) - $deletedRecord,
                ]),
        ]);
    }
}
