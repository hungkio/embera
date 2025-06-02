<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\TaxonSortRequest;
use App\Domain\Taxonomy\Models\Taxon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

class TaxonSortController
{
    use AuthorizesRequests;

    public function __invoke(TaxonSortRequest $request, Taxon $taxon): JsonResponse
    {
        $this->authorize('update', $taxon);
        $newSiblings = Taxon::whereParentId($request->input('parent_id'))->where('id', '<>', $taxon->id)
            ->ordered()
            ->pluck('id')
            ->toArray();
        $taxon->update(['parent_id' => $request->input('parent_id'), 'order_column' => $request->input('position')]);
        array_splice($newSiblings, (int)$request->input('position'), 0, $taxon->id);
        Taxon::setNewOrder($newSiblings);

        return response()->json(['status' => true]);

    }
}
