<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateMenuRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|max:255'
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Tên',
        ];
    }
}
