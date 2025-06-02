<?php

declare(strict_types=1);

namespace App\Domain\Taxonomy\Actions;

use App\Domain\Taxonomy\Models\Taxon;
use Illuminate\Support\Facades\DB;

class TaxonDeleteAction
{
    public function execute(Taxon $taxon): void
    {
        DB::transaction(function () use ($taxon){
            $taxon->delete();

            $taxon->descendants->each->delete();
        });
    }
}
