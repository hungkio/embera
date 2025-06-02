<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'search' => 'required|max:255'
        ];
    }

    public function messages(): array
    {
        return [
            'search.required' => 'Từ khoá tìm kiếm không được để trống!',
            'search.max' => 'Từ khoá tìm kiếm quá dài!'
        ];
    }

    public function attributes()
    {
        return [
            'search' => 'tìm kiếm',
        ];
    }
}
