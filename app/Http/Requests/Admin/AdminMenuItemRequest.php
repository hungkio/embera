<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminMenuItemRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'menu_id' => 'required',
            'type' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Tên menu',
            'type' => 'Loại',
        ];
    }
}
