<?php
declare(strict_types=1);

namespace App\Domain\Taxonomy\Actions;


use App\Domain\Taxonomy\Models\Taxon;
use App\Domain\Taxonomy\Models\Taxonomy;

class TaxonRenameAction
{
    public function execute(Taxon $taxon, string $name): void
    {
        $taxon->name = $name;
        $taxon->save();
        if ($taxon->parent_id == null){
            Taxonomy::whereId($taxon->taxonomy_id)->update(['name' => $name]);
        }
    }
}
