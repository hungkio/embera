<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BannerStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'image' => ['required'], ['image'], ['mimes:jpeg,png,jpg'],
            'title' => ['required', 'string', 'max:160', 'unique:banners,title,'.(isset($this->route('banner')->id) ? $this->route('banner')->id : '')],
            'link' => ['required'],
            'section' => ['required'],
            'status' => ['required'],
            'position' => ['required', 'numeric', 'max:1263', 'unique:banners,position,'.(isset($this->route('banner')->id) ? $this->route('banner')->id : '')],
        ];
    }

    public function attributes()
    {
        return [
            'title' => 'Tiêu đề',
            'image' => 'Ảnh',
            'link' => 'Đường dẫn',
            'section' => 'Phần',
            'status' => 'Trang thái',
            'position' => 'Vị trí',
        ];
    }
}
