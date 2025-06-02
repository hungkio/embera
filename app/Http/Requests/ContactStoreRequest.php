<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'numeric', 'digits_between:10,11'],
            'title' => ['required'],
            'message' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => 'Trường dữ liệu này không được để trống!',
            'first_name.string' => 'Giá trị trường không hợp lệ!',
            'last_name.required' => 'Trường dữ liệu này không được để trống!',
            'last_name.string' => 'Giá trị trường không hợp lệ!',
            'email.required' => 'Trường dữ liệu này không được để trống!',
            'email.email' => 'Giá trị email không hợp lệ!',
            'phone.required' => 'Trường dữ liệu này không được để trống!',
            'phone.numeric' => 'Trường này bắt buộc phải là số !',
            'phone.digits_between' => 'Giá trị trường không hợp lệ!',
            'title.required' => 'Trường dữ liệu này không được để trống!',
            'message.required' => 'Trường dữ liệu này không được để trống!',
        ];
    }
}
