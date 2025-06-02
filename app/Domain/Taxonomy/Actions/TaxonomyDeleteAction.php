<?php
declare(strict_types=1);

namespace App\Domain\Taxonomy\Actions;

use App\Domain\Taxonomy\Models\Taxonomy;
use Illuminate\Support\Facades\DB;

class TaxonomyDeleteAction
{
    public function execute(Taxonomy $taxonomy): void
    {
        DB::transaction(function () use ($taxonomy){
            $taxonomy->delete();
            $taxonomy->taxons->each->delete();
        });
    }
}
