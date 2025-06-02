<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class AdminRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', $this->emailUniqueRule(), 'regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/'],
            'password' => $this->passwordRule(),
            'roles' => ['required', 'exists:roles,id'],
        ];
    }

    protected function emailUniqueRule(): Unique
    {
        $rule = Rule::unique('admins')->whereNull('deleted_at');
        if ($this->route()->getName() === 'admin.admins.update') {
            $adminId = $this->route('admin')->id;

            return $rule->ignore($adminId);
        }

        return $rule;
    }

    protected function passwordRule()
    {
        $rule = ['min:8', 'confirmed'];

        if ($this->route()->getName() === 'admin.admins.update') {
            array_unshift($rule, 'nullable');
        } else {
            array_unshift($rule, 'required');
        }

        return $rule;
    }

    public function messages()
    {
        return [
            'email.email' => 'Giá trị email không hợp lệ!',
            'email.max' => 'Giá trị email không hợp lệ!',
            'email.regex' => 'Giá trị email không hợp lệ!',
            'first_name.required' => 'Trường dữ liệu này không được để trống!',
            'last_name.required' => 'Trường dữ liệu này không được để trống!',
        ];
    }

    public function attributes()
    {
        return [
            'roles' => 'quyền',
        ];
    }
}
