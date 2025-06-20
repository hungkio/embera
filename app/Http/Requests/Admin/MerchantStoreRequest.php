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
            'upload' => 'nullable|file|mimes:pdf|max:2048',
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
            'admin_id.exists' => 'Admin ID không hợp lệ.',
            'upload.mimes' => 'Tập tin phải là định dạng PDF.',
            'upload.max' => 'Tập tin không được vượt quá 2MB.',
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        // KHÔNG hash nữa, chỉ lưu password plain text
        if (isset($data['password'])) {
            session()->put('temp_password', $data['password']);
            // Không làm gì nữa, giữ nguyên
        }

        return $data;
    }

}
