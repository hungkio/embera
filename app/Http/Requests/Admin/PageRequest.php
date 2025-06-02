<?php

namespace App\Http\Requests\Admin;

use App\Enums\PageGroup;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Http\FormRequest;

class PageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255', 'regex:/^[0-9 a-zA-Z_ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼẾỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệếỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳýỵỷỹ,;.]+$/'],
            'body' => ['required'],
            'status' => ['sometimes', 'string'],
            'slug' => ['nullable', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'max:255', 'unique:pages,slug,'.(isset($this->route('page')->id) ? $this->route('page')->id : '')],
            'meta_title' => ['nullable', 'max:255', 'regex:/^[0-9 a-zA-Z_ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼẾỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệếỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳýỵỷỹ,;.]+$/'],
            'meta_description' => ['nullable', 'max:255', 'regex:/^[0-9 a-zA-Z_ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼẾỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệếỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳýỵỷỹ,;.]+$/'],
            'meta_keywords' => ['nullable', 'max:255', 'regex:/^[0-9 a-zA-Z_ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼẾỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệếỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳýỵỷỹ,;.]+$/'],
        ];
    }

    public function attributes()
    {
        return [
            'title' => 'tiêu đề',
            'body' => 'nội dung',
            'meta_title' => 'tiêu đề',
            'meta_description' => 'mô tả',
            'meta_keywords' => 'từ khóa',
            'slug' => 'đường dẫn',
        ];
    }

    public function messages()
    {
        return [
            'title.regex' => 'Không cho phép nhập ký tự đặc biệt!',
            'meta_title.regex' => 'Không cho phép nhập ký tự đặc biệt!',
            'meta_description.regex' => 'Không cho phép nhập ký tự đặc biệt!',
            'meta_keywords.regex' => 'Không cho phép nhập ký tự đặc biệt!',
            'slug.regex' => 'Không cho phép nhập ký tự đặc biệt!',
        ];
    }
}
