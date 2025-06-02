<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ToolMetaSeoPageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'meta_title' => 'required',
            'meta_keyword' => 'required',
            'meta_description' => 'required',
            'content_header' => 'required',
            'content_footer' => 'required',
            'meta_title_en' => 'required',
            'meta_keyword_en' => 'required',
            'meta_description_en' => 'required',
            'content_header_en' => 'required',
            'content_footer_en' => 'required',
            'page' => 'required',
            'name' => 'required'
        ];
    }

}
