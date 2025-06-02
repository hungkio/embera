<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Rules\OldPasswordRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('admins')->ignore(auth()->id())],
            'new_password' => ['nullable', 'confirmed', 'min:8', 'max:255'],
            'old_password' => ['nullable', 'required_with:new_password', 'max:255', new OldPasswordRule],
            'avatar' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'first_name' => 'Họ',
            'last_name' => 'Tên',
            'email' => 'Email',
            'new_password' => 'mật khẩu mới',
            'old_password' => 'mật khẩu cũ',
            'avatar' => 'Ảnh đại diện',
        ];
    }
}
