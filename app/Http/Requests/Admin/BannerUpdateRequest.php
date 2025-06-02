<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BannerUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg'],
            'title' => ['required', 'string', 'max:160', 'unique:banners,title,'.(isset($this->route('banner')->id) ? $this->route('banner')->id : '')],
            'link' => ['required'],
            'section' => ['required'],
            'status' => ['required'],
            'position' => ['required', 'numeric', 'unique:banners,position,'.(isset($this->route('banner')->id) ? $this->route('banner')->id : '')],
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
