<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CountryUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'flag' => ['nullable', 'image', 'mimes:jpeg,png,jpg'],
            'name' => ['required', 'string', 'max:160'],
            'code' => ['required', 'string', 'unique:countries,code,'.(isset($this->route('country')->id) ? $this->route('country')->id : '')],
        ];
    }

    public function attributes()
    {
        return [
            'image' => 'Ảnh',
            'title' => 'Tiêu đề',
            'link' => 'Đường dẫn',
            'section' => 'Phần',
            'status' => 'Trang thái',
            'position' => 'Vị trí',
        ];
    }
}
