<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Taxonomy\Models\Taxon;
use Illuminate\Http\Request;

class TaxonSearchController
{
    public function __invoke(Request $request)
    {
        $query = Taxon::query()
            ->with(['ancestors' => function ($q) {
                $q->breadthFirst();
            }])
            ->whereNotNull('parent_id');
        $taxons = $query->paginate();

        $taxons->getCollection()->transform(function ($taxon) {
            $result = [
                'id' => $taxon->id,
            ];
            $prettyName = '';
            if ($taxon->ancestors->isNotEmpty()) {
                foreach ($taxon->ancestors as $ancestor) {
                    $prettyName .= $ancestor->name.' -> ';
                }
            }
            $prettyName .= $taxon->name;
            $result['pretty_name'] = $prettyName;

            return $result;
        });

        return response()->json($taxons);
    }
}
