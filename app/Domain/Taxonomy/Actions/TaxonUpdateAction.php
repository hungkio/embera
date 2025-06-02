<?php

declare(strict_types=1);

namespace App\Domain\Taxonomy\Actions;

use App\Domain\Taxonomy\DTO\TaxonUpdateData;
use App\Domain\Taxonomy\Models\Taxon;
use Illuminate\Support\Facades\DB;

class TaxonUpdateAction
{
    public function execute(Taxon $taxon, TaxonUpdateData $data): void
    {
        DB::transaction(function () use ($taxon, $data){
            $taxon->name = $data->name;
            $taxon->slug = $data->slug;
            $taxon->description = $data->description;
            $taxon->meta_title = $data->meta_title;
            $taxon->meta_title_vi = $data->meta_title_vi;
            $taxon->meta_description = $data->meta_description;
            $taxon->meta_description_vi = $data->meta_description_vi;
            $taxon->meta_keywords = $data->meta_keywords;
            $taxon->meta_keywords_vi = $data->meta_keywords_vi;

            $taxon->content = $data->content;
            $taxon->content_vi = $data->content_vi;
            $taxon->content_bottom_vi = $data->content_bottom_vi;
            $taxon->content_bottom = $data->content_bottom;

            $taxon->save();

            if (!empty($data->icon)) {
                $taxon->addMedia($data->icon)->toMediaCollection('icon');
            }
        });
    }
}
