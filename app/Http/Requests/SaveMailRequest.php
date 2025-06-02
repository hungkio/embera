<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveMailRequest extends FormRequest
{
    public function rules(): array
    {
        $rule = [
            'tab' => 'required|unique:mail_settings,slug'
        ];
        if ($this->default_subject) {
            $rule = array_merge($rule, [
                'default_subject' => 'required|max:255',
            ]);
            unset($rule['tab']);
        }
        if ($this->default_user) {
            $rule = array_merge($rule, [
                'default_user' => 'required',
            ]);
        }
        if ($this->default_all) {
            $rule = array_merge($rule, [
                'default_all' => 'required',
            ]);
        }
        return $rule;
    }

    public function attributes()
    {
        return [
            'tab' => 'Tên chiến dịch'
        ];
    }
}
