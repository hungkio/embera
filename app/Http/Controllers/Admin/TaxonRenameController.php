<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\TaxonRenameRequest;
use App\Http\Requests\Admin\TaxonSortRequest;
use App\Domain\Taxonomy\Actions\TaxonRenameAction;
use App\Domain\Taxonomy\Models\Taxon;
use Illuminate\Http\JsonResponse;

class TaxonRenameController
{
    public function __invoke(TaxonRenameRequest $request, Taxon $taxon, TaxonRenameAction $action): JsonResponse
    {
        $action->execute($taxon, $request->input('name'));
        return response()->json(['status' => true, 'taxon' => $taxon]);
    }
}
