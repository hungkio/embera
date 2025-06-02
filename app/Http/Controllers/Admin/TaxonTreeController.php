<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Taxonomy\Models\Taxon;
use App\Domain\Taxonomy\Models\Taxonomy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaxonTreeController
{
    public function __invoke(Taxonomy $taxonomy, Taxon $taxon): JsonResponse
    {
        $tree = $taxon->children()
            ->withCount('children')
            ->ordered()
            ->get()
            ->map(function ($child) {
                return ['id' => $child->id, 'text' => $child->name, 'children' => $child->children_count > 0];
            });
        return response()->json($tree);

    }
}
