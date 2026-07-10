<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ContractStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sign_date' => 'required|date',
            'expired_date' => 'required|date|after_or_equal:sign_date',
            'status' => 'required|in:0,1,2',
            'expired_time' => 'nullable|string',
            'bank_info' => 'required|string',
            'bank_account_number' => 'required|string|max:100',
            'bank_account_name' => 'required|string|max:100',
            'email' => 'nullable|email',
            'customer_name' => 'required|string|max:255',
            'customer_cccd' => 'required|string|max:12',
            'customer_position' => 'required|string',
            'merchant_id' => 'required|exists:merchants,id',
            'shop_ids' => 'nullable|array',
            'shop_ids.*' => 'exists:shops,id',
            'admin_id' => 'nullable|exists:admins,id',
            'title' => 'required|string',
            'ceo_sign' => 'required|string',
            'location' => 'required|string',
            'city' => 'nullable|string|max:100',
            'note' => 'nullable|string',
            'upload' => 'nullable|file|mimes:pdf',
            'download_count' => 'nullable|integer|min:0',
            'business_registration' => 'required|string|regex:/^([0-9]{12}|[0-9]{10}(-[0-9]{3})?|[0-9]{13})$/', // CCCD (12 số) hoặc MST (10, 13 số có/không có gạch ngang)
        ];
    }

    public function messages(): array
    {
        return [
            'sign_date.required' => 'Ngày ký là trường bắt buộc.',
            'sign_date.date' => 'Ngày ký không hợp lệ.',

/*            'expired_date.required' => 'Ngày hết hạn là trường bắt buộc.',
            'expired_date.date' => 'Ngày hết hạn không hợp lệ.',
            'expired_date.after_or_equal' => 'Ngày hết hạn phải lớn hơn hoặc bằng ngày ký.',*/

            'status.required' => 'Trạng thái là trường bắt buộc.',
            'status.in' => 'Trạng thái không hợp lệ.',

            'bank_info.required' => 'Ngân hàng là trường bắt buộc.',
            'bank_info.string' => 'Ngân hàng không hợp lệ.',

            'bank_account_number.required' => 'Số tài khoản ngân hàng là trường bắt buộc.',
            'bank_account_number.string' => 'Số tài khoản không hợp lệ.',
            'bank_account_number.max' => 'Số tài khoản không được quá 100 ký tự.',

            'bank_account_name.required' => 'Tên chủ tài khoản là trường bắt buộc.',
            'bank_account_name.string' => 'Tên chủ tài khoản không hợp lệ.',
            'bank_account_name.max' => 'Tên chủ tài khoản không được quá 100 ký tự.',

            'email.email' => 'Email không hợp lệ.',

            'customer_name.required' => 'Tên khách là trường bắt buộc.',
            'customer_name.string' => 'Tên khách hàng không hợp lệ.',

            'customer_cccd.required' => 'CCCD khách hàng là trường bắt buộc.',
            'customer_cccd.string' => 'CCCD khách hàng không hợp lệ.',
            'customer_cccd.max' => 'CCCD khách hàng không được quá 12 ký tự.',

            'customer_position.required' => 'Chức vụ khách hàng là trường bắt buộc.',

            'merchant_id.required' => 'Merchant là trường bắt buộc.',
            'merchant_id.exists' => 'Merchant không hợp lệ.',

            'shop_ids.array' => 'Danh sách cửa hàng không hợp lệ.',
            'shop_ids.*.exists' => 'Một hoặc nhiều cửa hàng không tồn tại.',

            'admin_id.exists' => 'Admin không hợp lệ.',

            'title.required' => 'Tiêu đề là trường bắt buộc.',
            'title.string' => 'Tiêu đề không hợp lệ.',

            'ceo_sign.required' => 'Giám đốc ký là trường bắt buộc.',
            'ceo_sign.string' => 'Tên giám đốc không hợp lệ.',

            'location.required' => 'Địa điểm là trường bắt buộc.',
            'location.string' => 'Địa điểm không hợp lệ.',

            'note.string' => 'Ghi chú không hợp lệ.',

            'upload.file' => 'Tập tin không hợp lệ.',
            'upload.mimes' => 'Tập tin phải có định dạng PDF.',

            'download_count.integer' => 'Số lượt tải phải là số.',
            'download_count.min' => 'Số lượt tải không được âm.',
            'business_registration.required' => 'Mã số thuế/CCCD/GPKD là bắt buộc.',
            'business_registration.regex' => 'Định dạng giấy đăng ký kinh doanh/CCCD không hợp lệ (yêu cầu CCCD 12 số, hoặc MST 10 số, hoặc MST 13 số dạng 10số-3số).',
        ];
    }
}
