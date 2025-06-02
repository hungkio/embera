<?php
declare(strict_types=1);

namespace App\Domain\Taxonomy\Actions;

use App\Domain\Taxonomy\Models\Taxonomy;
use Illuminate\Support\Facades\DB;

class TaxonomyCreateAction
{
    public function execute(array $input): Taxonomy
    {
        $taxonomy = new Taxonomy;
        DB::transaction(function () use ($taxonomy, $input) {
            $taxonomy->name = $input['name'];
            $taxonomy->save();
            // Create root taxon
            $taxonomy->taxons()->create(['name' => $taxonomy->name]);
        });

        return $taxonomy;
    }
}
