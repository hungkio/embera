<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RoleBulkDeleteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required', 'array'],
        ];
    }
}
