<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MerchantStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => 'required|string|unique:merchants,username',
            'email' => 'required|email|unique:merchants,email',
            'phone' => 'nullable|string',
            'password' => 'required|string|min:6',
            'admin_id' => 'required|exists:admins,id',
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'Tên đăng nhập là trường bắt buộc.',
            'username.unique' => 'Tên đăng nhập đã được sử dụng.',
            'email.required' => 'Email là trường bắt buộc.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email đã được sử dụng.',
            'password.required' => 'Mật khẩu là trường bắt buộc.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'admin_id.exists' => 'BD không hợp lệ.',
        ];
    }
}
