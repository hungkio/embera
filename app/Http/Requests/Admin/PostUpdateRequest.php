<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PostUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255', 'regex:/^[0-9 a-zA-Z_ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼẾỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệếỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳýỵỷỹ,;.]+$/'],
            'description' => ['required', 'string', 'max:255'],
            'status' => ['required'],
            'category' => ['required'],
            'image' => ['sometimes', 'image', 'mimes:jpeg,png,jpg'],
            'body' => ['required'],
            'meta_title' => ['nullable', 'string', 'max:255', 'regex:/^[0-9 a-zA-Z_ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼẾỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệếỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳýỵỷỹ,;.]+$/'],
            'meta_description' => ['nullable', 'string', 'max:255', 'regex:/^[0-9 a-zA-Z_ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼẾỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệếỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳýỵỷỹ,;.]+$/'],
            'meta_keywords' => ['nullable', 'string', 'max:255', 'regex:/^[0-9 a-zA-Z_ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼẾỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệếỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳýỵỷỹ,;.]+$/'],
            'slug' => ['nullable', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'max:255', 'unique:posts,slug,'.(isset($this->route('post')->id) ? $this->route('post')->id : '')],
        ];
    }

    public function attributes()
    {
        return [
            'title' => 'tiêu đề',
            'description' => 'mô tả',
            'body' => 'nội dung',
            'image' => 'ảnh',
            'category' => 'danh mục',
            'meta_title' => 'tiêu đề',
            'meta_description' => 'mô tả',
            'meta_keywords' => 'từ khóa',
            'slug' => 'đường dẫn',
        ];
    }

    public function messages()
    {
        return [
            'title.regex' => 'Không cho phép nhập ký tự đặc biệt',
            'meta_title.regex' => 'Không cho phép nhập ký tự đặc biệt',
            'meta_description.regex' => 'Không cho phép nhập ký tự đặc biệt',
            'meta_keywords.regex' => 'Không cho phép nhập ký tự đặc biệt',
        ];
    }
}
