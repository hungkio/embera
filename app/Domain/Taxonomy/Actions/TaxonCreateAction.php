<?php

declare(strict_types=1);

namespace App\Domain\Taxonomy\Actions;

use App\Domain\Taxonomy\DTO\TaxonCreateData;
use App\Domain\Taxonomy\Models\Taxon;

class TaxonCreateAction
{
    public function execute(TaxonCreateData $data): Taxon
    {
        $taxon = Taxon::create([
            'name' => $data->name,
            'order_column' => $data->order_column,
            'taxonomy_id' => $data->taxonomy_id,
            'parent_id' => $data->parent_id,
        ]);

        return $taxon;
    }
}
