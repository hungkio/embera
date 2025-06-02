<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TaxonSortRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'parent_id' => ['required', 'exists:taxons,id'],
            'position' => ['required', 'integer'],
        ];
    }

    public function attributes()
    {
        return [
            'position' => 'vị trí',
        ];
    }
}
