<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ShopStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shop_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'shop_type' => 'required|string|max:100',
            'contact_phone' => 'required|string|max:20',
            'strategy' => 'nullable|string|max:255',
            'area' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'contract_id' => 'required|exists:contracts,id',
            'is_deleted' => 'boolean',
            'share_rate' => 'required|numeric|min:0',
            'share_rate_type' => 'required|in:percentage,fixed',
            'is_bound' => ['required', 'in:0,1'],
            'device_json' => 'nullable|json',
        ];
    }

    public function messages(): array
    {
        return [
            'shop_name.required' => 'Tên shop là trường bắt buộc.',
            'contract_id.required' => 'Hợp đồng là trường bắt buộc.',
            'share_rate.numeric' => 'Phần trăm chia lợi nhuận phải là số.',
            'share_rate.min' => 'Phần trăm chia lợi nhuận phải từ 0.',
            'share_rate.max' => 'Phần trăm chia lợi nhuận không được vượt quá 100.',
            'device_json.json' => 'Thông tin thiết bị phải là định dạng JSON hợp lệ.',
        ];
    }
}
