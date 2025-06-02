<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Domain\Taxonomy\Models\Taxon;
use Illuminate\Foundation\Http\FormRequest;

class TaxonStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'integer'],
            'parent_id' => ['required', 'exists:taxons,id'],
        ];
    }

    public function parent(): Taxon
    {
        return once(function () {
            return Taxon::find($this->input('parent_id'));
        });
    }

    public function attributes()
    {
        return [
            'name' => 'danh mục',
            'position' => 'vị trí',
        ];
    }
}
