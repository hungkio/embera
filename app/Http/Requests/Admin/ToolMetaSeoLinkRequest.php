<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ToolMetaSeoLinkRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'redirect_link' => 'required',
            'meta_title_vi' => 'required',
            'meta_keyword_vi' => 'required',
            'meta_description_vi' => 'required',
            'content_header_vi' => 'required',
            'content_footer_vi' => 'required',
            'name' => 'required'
        ];
    }

}
