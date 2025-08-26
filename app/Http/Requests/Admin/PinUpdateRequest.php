
<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PinUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Thay bằng logic kiểm tra quyền nếu cần
    }

    public function rules()
    {
        return [
            'imei' => 'required|string|max:255|unique:pins,imei,' . $this->pin->id,
            'serial_number' => 'required|string|max:255|unique:pins,serial_number,' . $this->pin->id,
        ];
    }

    public function messages()
    {
        return [
            'imei.required' => 'IMEI là bắt buộc.',
            'imei.string' => 'IMEI phải là chuỗi ký tự.',
            'imei.max' => 'IMEI không được vượt quá 255 ký tự.',
            'imei.unique' => 'IMEI đã tồn tại.',
            'serial_number.required' => 'Serial Number là bắt buộc.',
            'serial_number.string' => 'Serial Number phải là chuỗi ký tự.',
            'serial_number.max' => 'Serial Number không được vượt quá 255 ký tự.',
            'serial_number.unique' => 'Serial Number đã tồn tại.',
        ];
    }
}
