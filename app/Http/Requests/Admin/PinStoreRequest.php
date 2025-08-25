<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PinStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Thay bằng logic kiểm tra quyền nếu cần
    }

    public function rules()
    {
        return [
            'imei' => 'required|unique:pins,imei',
            'serial_number' => 'required|unique:pins,serial_number',
        ];
    }

    public function messages()
    {
        return [
            'imei.required' => 'IMEI là bắt buộc.',
            'imei.unique' => 'IMEI đã tồn tại.',
            'serial_number.required' => 'Serial Number là bắt buộc.',
            'serial_number.unique' => 'Serial Number đã tồn tại.',
        ];
    }
}
