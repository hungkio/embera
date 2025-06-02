<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    public function rules(): array
    {
        $rule = [];
        $actionRequiredView = ['delete', 'create', 'update', 'send', 'edit', 'destroy', 'store'];

        foreach ($this->permissions as $key => $permission) {
            $action = explode(".", $key)[1];
            $permission_name = explode(".", $key)[0];

            if ($permission_name == 'menus' || $permission_name == 'log-activities') {
                if ($this->permissions[$permission_name . '.index'] != 1 && in_array($action, $actionRequiredView) && $permission == 1) {
                    $rule[$permission_name . '.index'] = 'required';
                }
            } else {
                if ($this->permissions[$permission_name . '.view'] != 1 && in_array($action, $actionRequiredView) && $permission == 1) {
                    $rule[$permission_name . '.view'] = 'required';
                }
            }
        }

        return array_merge($rule, [
            'display_name' => ['required', 'string', 'max:255'],
            'permissions' => ['required', 'array'],
        ]);
    }

    public function attributes(): array
    {
        return [
            'display_name' => 'Tên hiển thị',
            'pages.view' => 'xem trang',
            'posts.view' => 'xem bài viết',
            'banners.view' => 'xem banner',
            'log-activities.index' => 'xem lịch sử thao tác',
            'menus.index' => 'xem cài đặt Menu',
            'mail-settings.view' => 'xem cài đặt chiến dịch gửi Mail',
            'taxonomies.view' => 'xem loại danh mục',
            'option-types.view' => 'xem tùy chọn',
            'admins.view' => 'xem quản trị viên',
            'roles.view' => 'xem vai trò',
        ];
    }

    public function allowPermissions(): array
    {
        return array_keys(array_filter($this->input('permissions'), function ($value) {
            return $value == 1;
        }));
    }
}
