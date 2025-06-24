<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MerchantUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $merchantId = $this->route('merchant') ? $this->route('merchant')->id : null;

        return [
            'username' => 'required|string|unique:merchants,username,' . $merchantId,
            'email' => 'nullable|email|unique:merchants,email,' . $merchantId,
            'phone' => 'nullable|string',
            'password' => 'nullable|string|min:6',
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
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'admin_id.exists' => 'Admin ID không hợp lệ.',
        ];
    }

}
