<?php

namespace App\Support\Traits;

use App\Domain\Taxonomy\Models\Taxon;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Taxonable
{
    public function taxons(): MorphToMany
    {
        return $this
            ->morphToMany(Taxon::class, 'taxonable')
            ->ordered();
    }
}
