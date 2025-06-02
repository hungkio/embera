<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Taxonomy\Models\Taxonomy;
use Illuminate\Http\JsonResponse;

class TaxonomyTreeController
{
    public function __invoke(Taxonomy $taxonomy): JsonResponse
    {
        $rootTaxon = $taxonomy->rootTaxon;
        return response()->json([
            ['id' => $rootTaxon->id, 'text' => $rootTaxon->name, 'children' => true],
        ]);
    }
}
