<?php
declare(strict_types=1);

namespace App\Domain\Taxonomy\DTO;

use App\Http\Requests\Admin\TaxonStoreRequest;
use App\Domain\Taxonomy\Models\Taxon;
use Spatie\DataTransferObject\DataTransferObject;

class TaxonCreateData extends DataTransferObject
{
    public string $name;

    public int $parent_id;

    public string $order_column;

    public int $taxonomy_id;

    public static function fromRequest(TaxonStoreRequest $request): TaxonCreateData
    {
        return new self([
            'name' => $request->input('name'),
            'parent_id' => $request->parent()->id,
            'order_column' => $request->input('position'),
            'taxonomy_id' => $request->parent()->taxonomy_id
        ]);
    }

}
