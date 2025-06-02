<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\TaxonStoreRequest;
use App\Http\Requests\Admin\TaxonUpdateRequest;
use App\Domain\Taxonomy\Actions\TaxonCreateAction;
use App\Domain\Taxonomy\Actions\TaxonDeleteAction;
use App\Domain\Taxonomy\Actions\TaxonUpdateAction;
use App\Domain\Taxonomy\DTO\TaxonCreateData;
use App\Domain\Taxonomy\DTO\TaxonUpdateData;
use App\Domain\Taxonomy\Models\Taxon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TaxonController
{
    use AuthorizesRequests;

    public function store(TaxonStoreRequest $request, TaxonCreateAction $action): JsonResponse
    {
        $taxonData = TaxonCreateData::fromRequest($request);

        $taxon = $action->execute($taxonData);

        logActivity($taxon, 'create');

        return response()->json([
            'id' => $taxon->id,
        ]);
    }

    public function edit(Taxon $taxon): View
    {
        $taxon->load( 'media');
        return view('admin.catalogs.taxons.edit', compact('taxon'));
    }

    public function update(TaxonUpdateRequest $request, Taxon $taxon, TaxonUpdateAction $action)
    {
        $updateData = TaxonUpdateData::fromRequest($request);

        $action->execute($taxon, $updateData);

        logActivity($taxon, 'update');

        flash()->success(__('Danh mục ":model" đã được cập nhật thành công !', ['model' => $taxon->name]));

        return redirect()->back();
    }

    public function destroy(Taxon $taxon, TaxonDeleteAction $action): JsonResponse
    {
        logActivity($taxon, 'delete');

        $action->execute($taxon);

        return response()->json([
            'status' => true,
            'message' => __('Danh mục ":model" đã xóa thành công !', ['model' => $taxon->name])
        ]);
    }
}
