<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SubscribeGroupUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:subscribe_groups,name,'.($this->route('sub_group') ? $this->route('sub_group')->id : '')],
            'email_ids' => ['required'],
        ];
    }

}
