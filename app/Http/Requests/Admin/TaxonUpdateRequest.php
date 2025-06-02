<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TaxonUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|alpha_dash|max:255',
            'description' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'icon' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048']
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'tên danh mục',
            'slug' => 'đường dẫn',
            'description' => 'mô tả',
            'meta_title' => 'tiêu đề',
            'meta_description' => 'mô tả',
            'meta_keywords' => 'từ khóa',
        ];
    }
}
