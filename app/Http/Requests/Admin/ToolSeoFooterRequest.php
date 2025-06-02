<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ToolSeoFooterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'meta_canonical' => 'required',
            'content_vi' => 'required',
        ];
    }

}
